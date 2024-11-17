<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InvoiceController extends AbstractController
{

    public function __construct(
        private RequestStack $requestStack
    ) {}

    /**
     * @Route("/invoices/{id}", name="generate_transaction_invoice", methods={"GET"})
     */
    public function generateInvoicePdf(int $id, TransactionRepository $transactionRepository): Response
    {
        // Fetch transaction with details from the repository
        $transaction = $transactionRepository->findTransactionWithDetails($id);

        if (!$transaction) {
            throw $this->createNotFoundException('Transaction not found');
        }

        // Extract related data
        $basket = $transaction->getBasket();
        $event = $basket->getEvent();
        $owner = $basket->getOwner();

        // Security check: Admins or transaction owners only
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $owner) {
            throw new AccessDeniedException('You do not have permission to view this invoice.');
        }

        // Extract tickets and calculate total
        $tickets = [];
        $totalPrice = 0.0;
        foreach ($basket->getTickets() as $ticket) {
            $ticketType = $ticket->getTicketType();
            $tickets[] = [
                'title' => sprintf(
                    '%s (For: %s %s)',
                    $ticketType->getDescription(),
                    $ticket->getFirstName(),
                    $ticket->getLastName()
                ),
                'price' => $ticketType->getPrice(),
            ];
            $totalPrice += $ticketType->getPrice();
        }

        // Generate the absolute URL for the logo
        $request = $this->requestStack->getCurrentRequest();
        $logoPath = $request->getSchemeAndHttpHost() . '/images/emailbanner.png';

        // Prepare data for the template
        $invoiceData = [
            'sender_address' => $this->getParameter('invoice_sender_address'),
            'event_title' => $event->getTitle(),
            'event_date' => $event->getDate()->format('F j, Y'),
            'purchaser_name' => $owner->getFullName(),
            'purchaser_email' => $owner->getEmail(),
            'transaction_date' => $transaction->getCreatedAt()->format('F j, Y'),
            'payment_status' => ucfirst($transaction->getStatus()),
            'payment_status_class' => $transaction->getStatus() === 'APPROVED' ? 'paid' : 'not-paid',
            'tickets' => $tickets,
            'total_price' => $totalPrice,
            'logo_path' => $logoPath,
        ];

        // Render the Twig template to HTML
        $html = $this->renderView('invoice.html.twig', $invoiceData);

        // Configure Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);

        // Load HTML into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        // Return the PDF as a response
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="transaction-invoice.pdf"',
        ]);
    }
}

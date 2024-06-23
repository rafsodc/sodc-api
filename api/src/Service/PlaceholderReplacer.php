<?php

namespace App\Service;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

class PlaceholderReplacer
{
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function replacePlaceholders(array $entities, string $template): string
    {
        preg_match_all('/%(\w+)\.(\w+)%/', $template, $matches);
        
        foreach ($matches[0] as $index => $placeholder) {
            $entityKey = $matches[1][$index];
            $propertyPath = $matches[2][$index];
            
            if (!isset($entities[$entityKey])) {
                throw new \InvalidArgumentException(sprintf('The entity "%s" does not exist in the provided entities.', $entityKey));
            }
            
            try {
                $value = $this->propertyAccessor->getValue($entities[$entityKey], $this->convertToPropertyPath($propertyPath));
                $template = str_replace($placeholder, $value, $template);
            } catch (NoSuchPropertyException $e) {
                throw new \InvalidArgumentException(sprintf('The property "%s" does not exist on the entity "%s".', $propertyPath, get_class($entities[$entityKey])));
            }
        }

        return $template;
    }

    private function convertToPropertyPath(string $propertyPath): string
    {
        return lcfirst($propertyPath);
    }
}

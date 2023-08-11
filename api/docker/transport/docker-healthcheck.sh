#!/bin/sh

PROCESS="php /srv/api/bin/console messenger:consume"

restart_container() {
    echo "Stopping workers"
    php /srv/api/bin/console messenger:stop-workers
    exit 1
}

restart_at_random() {
	echo "Time greater than 45 mins.  Generating random number"
    # 1/90 chance of reset, based on 6 attemps over 15 minutes
    limit=$(shuf -i 0-89 -n 1)
    if [ $limit = 0 ]; then restart_container; fi
}

check_memory() {
	echo "Time less than 45 mins.  Checking memory usage."
    case 1:${1:--} in  
        # If it does not end in an m, and is not a number, restart the container.
        (1:*[!m]|1:*[!0-9]*[m]) restart_container ;;
        # If member is above 128 restart container
        ($((${1%?}>128))*) restart_container ;;
    esac
}


# Check if the process exists
if output=$(ps -o etime,vsz,args | grep "$PROCESS"); then
    # Get the time in minutes
	time=$(echo $output | cut -d' ' -f1 | cut -d':' -f1)
    mem=$(echo $output | cut -d' ' -f2 )
	echo $time
	echo $mem
    case 1:${time:--}  in
        # If the time contains a 'd' or 'h', it will not be a number, so restart if it's not a number
        (1:*[!0-9]*) restart_container ;;
        # If the time is >= 45, we will restart the container randomly (to avoid both containers restarting at the same time) 
        ($((time>=45))*) restart_at_random ;;
        # Otherwise, check memory usage
        *) check_memory $mem;;
    esac
else
    restart_container
fi

exit 0
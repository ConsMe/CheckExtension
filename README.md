# CheckExtension
setup user hiro  
git clone https://github.com/ConsMe/CheckExtension.git to /home/hiro/  
install LEMP  
composer install  
php artisan migrate  
php artisan db:seed  
add to cron '* * * * * cd /home/hiro/CheckExtension && php artisan schedule:run >> /dev/null 2>&1'  
после обновления почистить кеши ларавеля  


# CheckExtension
setup user hiro  
git clone https://github.com/ConsMe/CheckExtension.git to /home/hiro/  
install LEMP  
sudo timedatectl set-timezone Europe/Moscow  
composer install  
php artisan migrate  
php artisan db:seed  
set telegram webhook  
add to cron '* * * * * cd /home/hiro/CheckExtension && php artisan schedule:run >> /dev/null 2>&1'  
после обновления почистить кеши ларавеля  
если изменилось расширение, изменить версию  в манифесте, запаковать его и поместить в папку storage/app, изменить версию расширение в .env  


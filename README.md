### DevOps Ödevi

#### 1.  Rocky 9, Debian 12 ya da Ubuntu LTS Server 22.04 dağıtımlarından birini kullanmalısınız. Dağıtım en az sayıda paketle kurulmuş olmalı ve servisin kendisiyle ilgili olmayan ek paketler kurulmamalı (ör: X sunucu) 
- VirtualBox kurulur
- Ubuntu 22.04 server iso dosyası indirilir
- VirtualBox'a iso dosyası ile Ubuntu kurulur
- Ubuntu minimized tercih edilir
- Kurulumun ardından belirlenen kullanıcı adı ve şifre ile giriş yapılır
- Sunucunun yapılandırılması için cloud-init kurulumu yapılır

#### 2.  Bir kullanıcı oluşturmalısınız (root dışında). O kullanıcıya kendi (host) bilgisayarından SSH ile bağlanmalısınız. Bağlantı anahtar (key) temelli gerçekleştirilmeli. SSH servisi için parola ile doğrulamayı kapatın.
- Lokal bilgisayarda terminalde aşağıdaki komut kullanılarak ssh key oluşturulur
```sh 
ssh-keygen
```
- ssh key bilgisayarın .ssh/id_rsa.pub dosyası içerisine kaydedilecektir
- Oluşturulan ssh anahtarı sanal sunucunun ssh/authorized_keys dosyalarının içerisine aşağıdaki komutla kopyalanır
```sh
scp .ssh/id_rsa.pub yaki@192.168.x.x:~/yaki_rsa.pub
```
- Şimdi sanal sunucuya kopyalanan dosyadaki key'i izin verilen keyler içerisine yazma vakti, aşağıdaki komut ile bunu yapmak mümkün
```sh
cat yaki_rsa.pub >> .ssh/authorized_keys
```

#### 3. Güvenlik duvarını SSH ve web servisi dışında herhangi bir isteği kabul etmeyecek biçimde ayarlayın.
- Bunun için aşağıdaki komutla UFW kurulumu yapılır. UFW, güvenlik duvarı kurallarını yönetmeyi sağlamaktadır
```sh
apt install ufw
```
- Aşağıdaki komutu kullanarak aktif olup olmadığını sorgulayabilirsiniz
```sh
systemctl status ufw
```
- Aktiflikten emin olduktan sonra aşağıdaki komutlar ile kurallar belirlenir
```sh
ufw default allow outgoing
ufw default deny incoming
ufw allow ssh
ufw allow http/tcp
```
- Bu sayede dışarı paket göndermek mümkünken, içeriye paket gönderilmesi engellenir ve ssh, http/tcp bağlantılarına izin verilir
```sh
ufw status
ufw enable
```
- Kurulum ardından ssh, http ve https trafiğine izin verilir
- Aşağıdaki komut kullanılarak geri kalan bütün bağlantılar reddedilir
```sh
sudo ufw default deny
```

#### 4. Apache veya Nginx web sunucu kurun.
- Apache sunucusu kurulumu yapılır
```sh
sudo apt install apache2
```
- Ardından serveri yapılandırabilmek için /var/www dosyası içerisine oluşturulacak web siteleri için dosya açılır
- Açılan klasörler için aşağıdaki komut kullanılarak, dosyanın sahibi için okuma, yazma ve yürütme, dosyanın grubu için okuma ve yürütme, diğer kullanıcıların ise sadece okuma izni verilir
```sh
cd /var/www
mkdir bugdayorg
chmod -R 755 /var/www/bugdayorg
```

#### 5. bugday.org için, Wordpress'in son sürümünü kurun.
- Wordpress kurmak için önce php, modülleri ve mysql kurulumu yapılır
```sh
apt install -y php php-{common,mysql,xml,xmlrpc,curl,gd,imagick,cli,dev,imap,mbstring,opcache,soap,zip,intl}
apt install mariadb-server mariadb-client
 ```
- mysql kurulumu yapılır
```sh
mysql_secure_installation
```
- Daha sonra şifre belirlenir ve database oluşturulur
- Database ile kullanıcı eşleştirilir
```sh
mysql
CREATE USER 'yaki'@'ozgur' IDENTIFIED BY '456789';
create database ozgur_db;
GRANT ALL PRIVILEGES ON ozgur_db.* TO 'yaki'@'ozgur';
FLUSH PRIVILEGES;
```
- Gerekli altyapı sağlandıktan sonra wordpress kurulumu yapılır
- Kurulan wordpress /var/www/bugdayorg dosyası içerisine atılır
```sh
wget https://wordpress.org/latest.zip
unzip latest.zip
mv wordpress/ /var/www/bugdayorg/
```
- wordpress dosyası içinde 755 şeklinde izin verilir
- Sonrasında apache sunucusu içerisinde sites-available dosyası içerisine .conf uzantılı bir dosya açılır
```sh
vim /etc/apache2/sites-available/bugdayorg.conf
```
- Bu dosya içerisine host, server admini, döküman yolu, server adı gibi bilgiler yazılır.
- Ardından oluşturulan conf dosyası aşağıdaki komutla varsayılan olarak güncellenir
- Varsayılanları .conf dosyaları deaktif edilir ve sunucu resetlenir
```sh
a2ensite bugdayorg.conf
a2enmod rewrite
a2dissite 000-default.conf
systemctl restart apache2
```
- Şimdi tarayıcıda sanal sunucu ip'si ile wordpress arayüzüne erişilebilir
- Açılan arayüzde kullanıcı adı, şifre ve database ismi girilir

#### 6.  Wordpress'te yeni bir yazı (post) yazın ve o yazıya bir dosya yükleyin. Yeni yazınıza SEO-uyumlu bir URL'den ulaşabilmelisiniz
- Wordpress başlangıç ekranında sayfa eklemek mümkün
- Butona tıklayarak sayfaya istenilen yazı ve dosya eklenebilir
- Ardından SEO'ya uygun şekilde sayfa isimlendirilir

#### 7. Aynı Wordpress'in hem bugday.org hem de buğday.org ile erişilebilir olması gerekiyor.
- Tekrardan conf dosyasını aşağıdaki gibi düzenleyerek iki farklı domain adresinin aynı wordpress sitesine erişmesini sağladık
```sh
<VirtualHost *:80>
ServerAdmin admin@ozgur.com
DocumentRoot /var/www/bugday.org/wordpress
ServerName www.bugday.org
ServerAlias www.bugday.org
<Directory /var/www/bugday.org/wordpress/>
Options FollowSymLinks
AllowOverride All
Require all granted
</Directory>
ErrorLog ${APACHE_LOG_DIR}/error.log
CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

<VirtualHost *:80>
    ServerName www.buğday.org
    ServerAlias buğday.org
    DocumentRoot /var/www/bugday.org/wordpress
    <Directory /var/www/bugday.org/wordpress>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/buğday.org-error.log
    CustomLog ${APACHE_LOG_DIR}/buğday.org-access.log combined
</VirtualHost>
```

#### 8.  ozgurstaj2024.com için bir web sitesi oluşturun. Site için bir web sayfası oluşturun, içinde 100 kere "Kullanıcılarımın kişisel verilerini toplamayacağım." yazsın (her biri yeni bir satırda).
- /var/www içerisinde yeni bir dosya oluşturulur
- index.html dosyası açılır ve içerisi sitenin anasayfası olacak şekilde düzenlenir
- Bir for döngüsü ile 100 kere istenilen string sayfaya yazdırılır
- Yazılan koda repo içerisindeki index.php'den ulaşılabilir

#### 9. ozgurstaj2024.com/yonetim adresine giriş parola korumasına sahip olmalı, sadece "ad.soyad" kullanıcı adı ve "parola" parolası ile girilebilmeli.
- Bu adım için /var/www/ozgur içerisine yonetim.php dosyası oluşturulur
- php ile kullanıcı adı ve şifre sorgusunun koşulları yazılır
- aynı dosya içerisinde html ile input alanları ve buton eklenir bu sayede kullanıcından girdi alınır ve sorgulanır
- yazılan koda repo içerisindeki yonetim.php'den ulaşılabilir
- ayrıca oluşturulacak .htaccess dosyası ile ozgurstaj2024.com/yonetim yazıldığında yonetim.php dosyasının çalıştırılması sağlanır
```sh
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^/yonetim$ yonetim.php [NC,L]
RewriteRule ^/index.html$ index.html [NC,L]
```

#### 10. Web sitesi hem www.ozgurstaj2024.com hem de ozgurstaj2024.com adresinden erişilebilir olmalıdır.
- bugdayorg.conf dosyası içerisinde olduğu gibi 2 farklı virtualhost bloğu açılır ve gerekli yerler ozgurstaj2024 sitesi için doldurulur ve aynı siteye erişilmesi mümkün kılınır

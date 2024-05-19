# DevOps Ödevi

## 1.  Rocky 9, Debian 12 ya da Ubuntu LTS Server 22.04 dağıtımlarından birini kullanmalısınız.
Dağıtım en az sayıda paketle kurulmuş olmalı ve servisin kendisiyle ilgili olmayan ek paketler kurulmamalı (ör: X sunucu) 
    - VirtualBox kurulur
    - Ubuntu 22.04 server iso dosyası indirilir
    - VirtualBox'a iso dosyası ile Ubuntu kurulur
    - Ubuntu minimized tercih edilir
    - Kurulumun ardından belirlenen kullanıcı adı ve şifre ile giriş yapılır
    - Sunucunun yapılandırılması için cloud-init kurulumu yapılır

2.  ## Bir kullanıcı oluşturmalısınız (root dışında). O kullanıcıya kendi (host) bilgisayarından SSH ile bağlanmalısınız. Bağlantı anahtar (key) temelli gerçekleştirilmeli. 
    ## SSH servisi için parola ile doğrulamayı kapatın.
    - Lokal bilgisayarda terminalde aşağıdaki komut kullanılarak ssh key oluşturulur
    ```sh 
    ssh-keygen
    ```
    - Oluşturulan ssh anahtarı sanal sunucunun ssh/authorized_keys dosyalarının içerisine kopyalanır

3. ## Güvenlik duvarını SSH ve web servisi dışında herhangi bir isteği kabul etmeyecek biçimde ayarlayın.
    - Bunun için UFW kurulumu yapılır. UFW, güvenlik duvarı kurallarını yönetmeyi sağlamaktadır
    - Kurulum ardından ssh, http ve https trafiğine izin verilir
    - Aşağıdaki komut kullanılarak geri kalan bütün bağlantılar reddedilir
    ```sh
    sudo ufw default deny
    ```

4. ## Apache veya Nginx web sunucu kurun.
    - Apache sunucusu kurulumu yapılır
    - Ardından serveri yapılandırabilmek için /var/www dosyası içerisine oluşturulacak web siteleri için dosya açılır
    - Açılan klasörler için aşağıdaki komut kullanılarak, dosyanın sahibi için okuma, yazma ve yürütme, dosyanın grubu için okuma ve yürütme, diğer kullanıcıların ise sadece okuma izni verilir
    ```sh 
    chmod -R 755 /var/www/bugdayorg şe
    ```

5. ## bugday.org için, Wordpress'in son sürümünü kurun.
    - Wordpress kurmak için önce php ve mysql kurulumu yapılır
    - mysql kurulumundan sonra şifre belirlenir ve database oluşturulur
    - Database ile kullanıcı eşleştirilir
    - Gerekli altyapı sağlandıktan sonra wordpress kurulumu yapılır
    - Kurulan wordpress /var/www/bugdayorg dosyası içerisine atılır
    - wordpress dosyası içinde 755 şeklinde izin verilir
    - Sonrasında apache sunucusu içerisinde sites-available dosyası içerisine .conf uzantılı bir dosya açılır
    - Bu dosya içerisine host, server admini, döküman yolu, server adı gibi bilgiler yazılır.
    - Ardından oluşturulan conf dosyası aşağıdaki komutla varsayılan olarak güncellenir
    ```sh
    a2ensite x.conf
    ```
    - Varsayılanları .conf dosyaları deaktif edilir ve sunucu resetlenir
    - Şimdi tarayıcıda sanal sunucu ip'si ile wordpress arayüzüne erişilebilir
    - Açılan arayüzde kullanıcı adı, şifre ve database ismi girilir

6.  ## Wordpress'te yeni bir yazı (post) yazın ve o yazıya bir dosya yükleyin.
    ## Yeni yazınıza SEO-uyumlu bir URL'den ulaşabilmelisiniz
    - Wordpress başlangıç ekranında sayfa eklemek mümkün
    - Butona tıklayarak sayfaya istenilen yazı ve dosya eklenebilir
    - Ardından SEO'ya uygun şekilde sayfa isimlendirilir

7. ## Aynı Wordpress'in hem bugday.org hem de buğday.org ile erişilebilir olması gerekiyor.
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

8.  ## ozgurstaj2024.com için bir web sitesi oluşturun.
    ## Site için bir web sayfası oluşturun, içinde 100 kere "Kullanıcılarımın kişisel verilerini toplamayacağım." yazsın (her biri yeni bir satırda).
    - /var/www içerisinde yeni bir dosya oluşturulur
    - index.html dosyası açılır ve içerisi sitenin anasayfası olacak şekilde düzenlenir
    - Bir for döngüsü ile 100 kere istenilen string sayfaya yazdırılır

9. ## ozgurstaj2024.com/yonetim adresine giriş parola korumasına sahip olmalı, sadece "ad.soyad" kullanıcı adı ve "parola" parolası ile girilebilmeli.
    - Bu adım için /var/www/ozgur içerisine yonetim.php dosyası oluşturulur
    - php ile kullanıcı adı ve şifre sorgusunun koşulları yazılır
    - aynı dosya içerisinde html ile input alanları ve buton eklenir bu sayede kullanıcından girdi alınır ve sorgulanır 
    - ayrıca oluşturulacak .htaccess dosyası ile ozgurstaj2024.com/yonetim yazıldığında yonetim.php dosyasının çalıştırılması sağlanır

10. ## Web sitesi hem www.ozgurstaj2024.com hem de ozgurstaj2024.com adresinden erişilebilir olmalıdır.
    - bugdayorg.conf dosyası içerisindeki gibi 2 farklı virtualhost bloğu açılır ve gerekli yerler istenildiği gibi doldurularak farklı dnsler ile aynı siteye erişilmesi mümkün kılınır

# Hostel Booking System

PHP + MySQL hostel booking app (login, register, book room, admin panel, reports).

## What was fixed
- Added `index.php` (entry point)
- Added `database/schema.sql` (users, rooms, bookings tables — was missing)
- `config/database.php` now reads DB credentials from environment variables (AWS-ready)
- Added `.gitignore`

**Default admin login:** `admin@hostel.com` / `admin123` (change this after first login).

---

## AWS Hosting Guide (EC2 + RDS)

### 1. Create MySQL database (RDS)
1. AWS Console → **RDS** → Create database
2. Engine: **MySQL**, template: **Free tier**
3. DB name: `hostel_booking_db`, set username/password
4. Public access: **Yes** (for setup; restrict later)
5. Note the **endpoint** (e.g. `xxx.rds.amazonaws.com`)

### 2. Launch server (EC2)
1. EC2 → Launch instance → **Ubuntu 22.04**, t2.micro (free tier)
2. Security group: allow ports **22** (SSH), **80** (HTTP), **443** (HTTPS)
3. Connect via SSH:
   ```bash
   ssh -i your-key.pem ubuntu@<EC2-PUBLIC-IP>
   ```

### 3. Install PHP + Apache + MySQL client
```bash
sudo apt update
sudo apt install -y apache2 php php-mysql php-mbstring mysql-client git
sudo systemctl enable apache2
```

### 4. Deploy the code
```bash
cd /var/www/html
sudo rm -f index.html
sudo git clone https://github.com/mkokelaadam63/hostel-booking-system.git .
```
⚠️ Use a fresh (non-leaked) GitHub token or SSH key if the repo is private.

### 5. Set environment variables
```bash
sudo nano /etc/apache2/envvars
```
Add at the bottom:
```
export DB_HOST="your-rds-endpoint.rds.amazonaws.com"
export DB_NAME="hostel_booking_db"
export DB_USER="your_db_user"
export DB_PASS="your_db_password"
```
Then load them in Apache config:
```bash
sudo nano /etc/apache2/apache2.conf
```
Add:
```
PassEnv DB_HOST DB_NAME DB_USER DB_PASS
```
Restart Apache:
```bash
sudo systemctl restart apache2
```

### 6. Import the database schema
```bash
mysql -h your-rds-endpoint.rds.amazonaws.com -u your_db_user -p < database/schema.sql
```

### 7. Fix permissions
```bash
sudo chown -R www-data:www-data /var/www/html
```

### 8. Test
Open `http://<EC2-PUBLIC-IP>/` in your browser → should load the login page.

### 9. (Recommended) Add HTTPS
```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```
(Needs a domain pointed at your EC2 IP.)

---

## Local development
1. Install XAMPP/MAMP (Apache + MySQL + PHP)
2. Put project in `htdocs`
3. Import `database/schema.sql` via phpMyAdmin
4. Visit `http://localhost/hostel-booking-system/`
5. Local mode works with no env vars set (defaults to `root`/no password).

# EduConnect - Educational Support Platform

EduConnect is a comprehensive web platform that connects students with donors and mentors to facilitate educational support, scholarships, and mentorship opportunities.

## Features

### For Students
- Create and manage student profiles
- Apply for scholarships
- Connect with mentors
- Schedule mentoring sessions
- Track scholarship applications
- Receive notifications and messages

### For Donors
- Create and manage scholarships
- Review scholarship applications
- Track impact and contributions
- Communicate with scholarship recipients
- View detailed analytics

### For Mentors
- Create mentor profiles with expertise and availability
- Connect with students
- Schedule and manage mentoring sessions
- Track mentoring progress
- Communicate with mentees

## Technical Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/educonnect.git
cd educonnect
```

2. Create a MySQL database and import the schema:
```bash
mysql -u root -p
source database.sql
```

3. Configure the database connection:
- Copy `config/database.php` to your project
- Update the database credentials in `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'educonnect');
```

4. Set up your web server:
- For Apache, ensure mod_rewrite is enabled
- Point your web root to the project directory
- Ensure proper permissions are set:
```bash
chmod 755 -R /path/to/educonnect
chmod 777 -R /path/to/educonnect/uploads
```

## Directory Structure

```
educonnect/
├── config/
│   └── database.php
├── css/
│   └── style.css
├── js/
│   └── main.js
├── dashboard/
│   ├── student.php
│   ├── donor.php
│   └── mentor.php
├── uploads/
├── index.html
├── login.php
├── register.php
├── logout.php
├── database.sql
└── README.md
```

## Security Features

- Password hashing using PHP's password_hash()
- Prepared statements for database queries
- Input sanitization
- Session management
- CSRF protection
- XSS prevention

## Usage

1. Visit the homepage and create an account as a student, donor, or mentor
2. Complete your profile with relevant information
3. Start connecting:
   - Students: Browse scholarships and mentors
   - Donors: Create scholarships and review applications
   - Mentors: Set availability and connect with students

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please email support@educonnect.com or create an issue in the repository.

## Acknowledgments

- Font Awesome for icons
- Bootstrap for responsive design inspiration
- PHP community for best practices and security guidelines 
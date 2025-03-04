# Job Portal MVC Mini-Application Documentation
Link: https://jobportal.onthis.website/
## 1. Overview
This project is a simple Job Portal built using native PHP and MySQL without relying on third-party frameworks. It demonstrates modern web development practices such as:

MVC Architecture: Separating concerns into Models, Views, and Controllers.
AJAX Enhancements: Live form validation, dynamic state/province population, and asynchronous form submission.
Security Best Practices: CSRF protection, input sanitization, file upload handling, and rate limiting.
Progressive Enhancement: Using $oldInput as a fallback when JavaScript is disabled.
Dashboard View: A modern, responsive dashboard implemented with Flexbox instead of traditional HTML tables.
Additional Features: Job title uniqueness checking, rate limiting based on IP and user agent, and dashboard optimization via database indexing.

## 2. Directory Structure

```plaintext

project-root/
├── app/
│   ├── config/               # Environment configurations (e.g., database.php)
│   ├── controllers/          # Controller logic for the form and dashboard (e.g., JobController.php, BaseController.php)
│   ├── models/               # Database interaction (e.g., Job.php)
│   ├── utils/                # Helper classes (e.g., RateLimiter.php, SendEmail.php)
│   └── views/                # HTML templates (e.g., form.php, dashboard.php)
├── public/
│   ├── assets/               # CSS, JS, and fonts
│   │   ├── scss/             # Sass source files
│   │   └── js/               # JavaScript files (e.g., form.js)
|   |── data/                 # JSON datasets (e.g., states.json for dynamic state/province population)
│   └── index.php             # Front controller (autoloads controllers and dispatches actions)
├── migrations/               # Database schema migration scripts (e.g., 001_initial_schema.sql)
├── uploads/                  # Uploaded files (stored outside the web root for security)
└── docs/                     # Project documentation (this file and architecture diagrams)
```
Additional Files
.htaccess (in public/):
Blocks direct access to sensitive directories like app/ and config/.

## 3. Installation & Setup
Prerequisites
-PHP 7.x or above (with PDO support)
-MySQL (or a compatible database, e.g., MariaDB)
-A command-line environment for running PHP’s built-in server

Installation Steps
- Clone the Repository:
    git clone https://github.com/ryan7998/job-portal.git
    cd job-portal
- Database Setup:
    Create a new database called job_submissions (or your preferred name).
    Run the migration script to set up the schema:
    mysql -u "your_username" -p job_submissions < migrations/001_initial_schema.sql
- Configure Database Credentials:
    Update the file app/config/database.php with your database host, name, username, and password.
- Set Folder Permissions:
    Ensure that the uploads/ folder is writable: chmod 755 uploads/
- Set Up .htaccess:
    The .htaccess file in the public folder blocks access to internal directories. Ensure it’s in place as per the project requirements.
- Run the Application:
    Use PHP’s built-in server by running the following from the project root (which now uses public/ as the document root):
        php -S localhost:8000 -t public
        Access the application at: http://localhost:8000

## 4. Key Features & Design Decisions
- MVC Architecture
    Controllers:
        JobController.php (and BaseController.php) handle form submissions, AJAX requests, and dashboard rendering.
    Models:
        Job.php encapsulates all database interactions using PDO with a Singleton pattern via Database.php.
    Views:
        Templates in app/views/ (e.g., form.php and dashboard.php) render HTML output, integrating CSRF tokens, ARIA attributes for accessibility, and fallbacks for JavaScript-disabled scenarios.
- AJAX and Frontend Enhancements
    AJAX Submission:
        Form submission is handled asynchronously using the Fetch API in public/assets/js/form.js, providing live feedback and error handling.
    Dynamic Data:
        The form checks job title uniqueness in real time via AJAX and dynamically populates state/province options from a JSON dataset located in the data/ folder.
    Fallback Handling:
        $oldInput is used to repopulate form fields if JavaScript fails, ensuring graceful degradation.
- File Upload Functionality
    File Handling:
        Uploaded files are stored in the uploads/ folder (outside of the public directory for security). The filename (with extension) is saved in the database.
    Secure Downloads:
        A public endpoint (download.php) allows users to download files securely without exposing the uploads folder directly.
- Security Enhancements
    CSRF Protection:
        CSRF tokens are generated and validated in the form and controller.
    Input Sanitization:
        Both client-side and server-side validation and sanitization are applied to all user inputs.
    Rate Limiting:
        A simple rate limiter is implemented in app/utils/RateLimiter.php to restrict excessive submissions, using session-based tracking of IP addresses and user agents.
    Database Optimization:
        The job title is set as UNIQUE and indexed to support AJAX uniqueness checks. Indexes on title and country improve dashboard filtering performance.

## 5. Additional Utilities
- SendEmail Utility
    SendEmail.php:
        A helper in app/utils/SendEmail.php encapsulates the logic for sending confirmation emails using PHP’s mail() function. This keeps the controller code clean.
    RateLimiter.php:
        A helper in app/utils/RateLimiter.php creates the logic to limit the requests by storing the timestamp and removing them if it exceeds the time limit.

## 6. Running the Application
- Start the PHP Server:
    From the project root, run:
        php -S localhost:8000 -t public
    The application will be accessible at http://localhost:8000.
- Testing the Features:
    Form Submission:
        Fill in the job submission form, ensuring that validations (AJAX and server-side) are working, and a confirmation email is sent upon successful submission.
    File Upload:
        Test the file upload feature, ensuring that files are stored in the uploads/ folder with correct names (including extensions).
    Dashboard:
        Navigate to the dashboard (e.g., via ?controller=job&action=getSubmissions) to see job submissions displayed in a Flexbox-based responsive layout.
    Download Files:
        Verify that download links correctly point to download.php and allow secure file downloads.

## 7. Conclusion
- This project serves as a comprehensive demonstration of building a modern web application using native PHP and MySQL, following best practices in security, usability, and maintainability. The use of AJAX for real-time validation and asynchronous form submissions, along with progressive enhancements and fallback strategies, ensures a robust user experience.

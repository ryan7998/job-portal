<?php
class SendEmail
{
    private $to;
    private $subject;
    private $headers;
    private $emailBody;

    public function __construct()
    {
        $this->headers = "From: jobform@voices.com\r\n";
        $this->headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    }
    // Send job submission confirmation email
    public function sendJobConfirmation(array $formData, $recipient = 'jobform@voices.com'): bool
    {
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        try {
            extract($formData);

            $this->to = filter_var($recipient, FILTER_SANITIZE_EMAIL);
            $this->subject = 'New Job Submission: ' . $title;
            $this->emailBody = sprintf(
                "Job Submission Details:\n\n" .
                    "Title: %s\n" .
                    "Budget: %s\n" .
                    "Location: %s, %s\n" .
                    "Script: %s\n" .
                    "File: %s\n" .
                    "Submitted At: %s",
                $title,
                strtoupper($budget),
                $country,
                $state,
                $script ?? 'N/A',
                $file_path ?? 'No file attached',
                date('Y-m-d H:i:s')
            );

            $logEntry = sprintf(
                "[%s] To: %s\nSubject: %s\nBody:\n%s\n\n",
                date('Y-m-d H:i:s'),
                $this->to,
                $this->subject,
                $this->emailBody
            );

            // commented out as smtp server is not set, instead create a log for the emails
            // return mail($this->to, $this->subject, $this->emailBody, $this->headers);
            file_put_contents(__DIR__ . '/../../logs/emails.log', $logEntry, FILE_APPEND);
            return true; // Simulate success
        } catch (ErrorException $e) {
            throw $e;
        }
    }
}

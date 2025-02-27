<?php
require_once __DIR__ . "/../config/database.php";

class Job
{
    public function save($data)
    {
        try {
            $db = Database::getConnection();
            $sql = "INSERT INTO jobs (title, script, country, state, file_path, budget, ip_address, user_agent)
                    VALUES (:title, :script, :country, :state, :file_path, :budget, :ip_address, :user_agent)";
            $stmt = $db->prepare($sql);
            $stmt->execute($data);
            return $db->lastInsertId();
        } catch (PDOException $e) {
            // // Handle unique title error:
            if ($e->errorInfo[1] === 1062) {
                throw new Exception('Job title already exists');
            }
            throw $e;
        }
    }

    public function getAllJobs()
    {
        // For later use in the dashboard:
        try {
            $db = Database::getConnection();
            $stmt = $db->query(
                "SELECT * FROM jobs ORDER BY created_at DESC"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function titleExists($title)
    {
        if (!$title) return false;
        try {
            $db = Database::getConnection();
            $sql = "SELECT title FROM jobs WHERE title = :title";
            $stmt = $db->prepare($sql);
            $stmt->execute(['title' => $title]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw $e;
        }
    }
}

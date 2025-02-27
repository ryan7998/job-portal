<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Submission Form</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css" />
</head>

<body>
    <div class="container">
        <div class="left-panel">
            <div class="inner">
                <h1>Job Submissions</h1>
            </div>
        </div>
        <div class="right-panel">
            <div class="inner">
                <div id="back-to-form">
                    <a class="form-group" href="/">
                        <i class="fa-solid fa-arrow-left"></i> Back to Form <i class="fa-solid fa-table"></i>
                    </a>
                </div>
                <div class="row" aria-label="Job submissions">
                    <?php if (!empty($jobs)): ?>
                        <?php foreach ($jobs as $job): ?>
                            <div class="dashboard-card">
                                <h2><?= htmlspecialchars($job['title']) ?></h2>
                                <div><strong>Script:</strong> <?= htmlspecialchars($job['script']) ?></div>
                                <div><strong>Country:</strong> <?= htmlspecialchars($job['country']) ?></div>
                                <div><strong>State:</strong> <?= htmlspecialchars($job['state']) ?></div>
                                <div><strong>Budget:</strong> <?= ['low' => '$5 - $99', 'medium' => '$100 - $249', 'high' => '$250 - $499'][$job['budget']] ?></div>
                                <div><strong>Submitted:</strong> <?= date('M j, Y H:i', strtotime($job['created_at']))  ?></div>
                                <?php if (!empty($job['file_path'])): ?>
                                    <a id="dashboard-button-wrapper" class="button" href="/download.php?file=<?= urlencode($job['file_path']) ?>" class="download-btn"><i class="fa-solid fa-download"></i>Download File</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No submissions found.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
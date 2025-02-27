<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Submission Form</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css" />
    <!-- Lazy load scripts using defer -->
    <script src="/assets/js/form.js" defer></script>
</head>

<body>
    <div class="container">
        <div class="left-panel">
            <div class="inner">
                <h1>Let us know your project requirements</h1>
            </div>
        </div>
        <div class="right-panel">
            <form class="" id="projectForm" action="/?controller=job&action=handleSubmit" method="POST" enctype="multipart/form-data" novalidate aria-labelledby="formTitle">
                <div class="inner">
                    <!-- Include CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <!-- hack around to check if js is enabled in the client browser: -->
                    <input type="hidden" name="js_enabled" id="js_enabled" value="false">

                    <div class="form-group">
                        <label for="title">What's your project’s name?</label>
                        <small>Provide a short, descriptive title to attract talent.</small>
                        <input type="text" id="title" name="title" required aria-required="true" aria-describedby="error_title" aria-invalid="<?= !empty($errors['title']) ? 'true' : 'false' ?>" placeholder="For example, '30 Second Radio Spot' or 'Corporate Training Video'" value="<?= htmlspecialchars($oldInput['title'] ?? '') ?>">
                        <div class="error" id="error_title" aria-live="polite"><?= $errors['title'] ?? '' ?></div>
                    </div>
                    <div class="form-group">
                        <label for="script">Small Script <span class="smaller">Optional</span></label>
                        <small>Include a small piece of a script you would like talent to read.</small>
                        <textarea id="script" name="script" aria-required="false" aria-describedby="error_script" aria-invalid="<?= !empty($errors['script']) ? 'true' : 'false' ?>" placeholder="Type or paste a sample script here."><?= htmlspecialchars($oldInput['script'] ?? '') ?></textarea>
                        <small id="wordCount">0 words</small>
                    </div>
                    <div id="location" class="row">
                        <div class="form-group">
                            <label for="country">Country</label>
                            <select id="country" name="country" required aria-required="true" aria-describedby="error_country" aria-invalid="<?= !empty($errors['country']) ? 'true' : 'false' ?>">
                                <option value="">Select your country</option>
                                <option value="CANADA" <?= ($oldInput['country'] ?? '') === 'CANADA' ? 'selected' : '' ?>>Canada</option>
                                <option value="USA" <?= ($oldInput['country'] ?? '') === 'USA' ? 'selected' : '' ?>>USA</option>
                            </select>
                            <div class="error" id="error_country" aria-live="polite"><?= $errors['country'] ?? '' ?></div>
                        </div>
                        <div class="form-group">
                            <label for="state">State/Province</label>
                            <select id="state" name="state" required aria-required="true" aria-describedby="error_country" aria-invalid="<?= !empty($errors['state']) ? 'true' : 'false' ?>">
                                <option>Select your state/province</option>
                                <?php if (!empty($stateData)): ?>
                                    <?php foreach ($stateData as $state): ?>
                                        <option value="<?= htmlspecialchars($state) ?>" <?= (isset($old['state']) && $old['state'] === $state) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($state) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="error" id="error_state" aria-live="polite"><?= $errors['state'] ?? '' ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file">Please upload your reference file here <span class="smaller">Optional</span></label>
                        <small>(Max. 20 mb)</small>
                        <div class="file-upload-wrapper">
                            <input type="file" id="file" name="file" aria-required="false" aria-describedby="file" aria-invalid="<?= !empty($errors['file']) ? 'true' : 'false' ?>">
                            <label for="file" class="file-upload-label">
                                <i class="fa-solid fa-upload"></i>
                                Browse for Files
                            </label>
                            <small class="file-name"></small>
                        </div>
                        <div class="error" id="error_file" aria-live="polite"></div>
                    </div>
                    <div class="form-group">
                        <label for="budget">What's your budget?</label>
                        <small>Minimum project cost is $5 USD</small>
                        <div id="budget-button-group" class="row">
                            <label><input type="radio" name="budget" value="low" required <?= (isset($oldInput['budget']) && $oldInput['budget'] === 'low') ? 'checked' : '' ?>>$5 - $99</label>
                            <label><input type="radio" name="budget" value="medium" required <?= (isset($oldInput['budget']) && $oldInput['budget'] === 'medium') ? 'checked' : '' ?>>$100 - $249</label>
                            <label><input type="radio" name="budget" value="high" required <?= (isset($oldInput['budget']) && $oldInput['budget'] === 'high') ? 'checked' : '' ?>>$250 - $499</label>
                        </div>
                        <div class="error" id="error_budget" aria-live="polite"><?= $errors['budget'] ?? '' ?></div>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="inner">
                    <div class="form-group form-group-buttons">
                        <button type="reset">Reset</button>
                        <button type="submit">
                            <div id="loading"></div>Submit
                        </button>
                    </div>
                    <div id="submissions" class="form-group">
                        <a href="?controller=dashboard&action=showdashboard">
                            <i class="fa-solid fa-database"></i> Submissions <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
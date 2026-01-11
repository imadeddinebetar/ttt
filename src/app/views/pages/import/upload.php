<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <style>
        :root {
            --primary-color: #6366f1;
            /* Indigo 500 */
            --primary-hover: #4f46e5;
            /* Indigo 600 */
            --bg-color: #0f172a;
            /* Slate 900 */
            --text-color: #f8fafc;
            /* Slate 50 */
            --card-bg: #1e293b;
            /* Slate 800 */
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 2rem 0;
            min-height: 100vh;
        }

        .upload-container {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            text-align: center;
            max-width: 400px;
            width: 100%;
            margin: 0 auto 2rem auto;
        }

        h1 {
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.5rem;
        }

        /* Hide the default file input */
        #file-upload {
            display: none;
        }

        /* Custom Button Styling */
        .upload-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);
            border: none;
            font-size: 1rem;
        }

        .upload-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(99, 102, 241, 0.5);
        }

        .upload-btn:active {
            transform: translateY(0);
        }

        .upload-icon {
            width: 20px;
            height: 20px;
        }

        /* Table Styles */
        .table-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        thead {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: white;
        }

        tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.1);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 1rem;
            font-size: 0.875rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }

        .status-completed {
            background-color: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-processing {
            background-color: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .status-failed {
            background-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .btn-process {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
        }

        .btn-process:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.4);
        }

        .btn-process:active {
            transform: translateY(0);
        }

        .btn-process:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>

<body>

    <div class="upload-container">
        <h1>Upload Document</h1>

        <!-- Form pointing to the route. Adjust action="/upload" to your actual route -->
        <form id="upload-form" action="/<?= env('APP_DIR') ?>imports/store" method="POST" enctype="multipart/form-data">
            <input type="file" id="file-upload" name="ssp_file" />

            <button type="button" class="upload-btn" onclick="document.getElementById('file-upload').click()">
                <svg xmlns="http://www.w3.org/2000/svg" class="upload-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Select &amp; Upload
            </button>
        </form>
    </div>

    <?php if (count($imports)): ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Import ID</th>
                        <th>Nbr Records</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- status-completed/status-processing/status-pending/status-failed  -->

                    <?php foreach ($imports as $import): ?>

                        <?php $status = $import['status']; ?>

                        <tr>
                            <td><?= e($import['id']) ?></td>
                            <td><?= e($import['records']) ?></td>
                            <td><?= e($import['created_at']) ?></td>
                            <td><span class="status-badge status-<?= $status ?>"><?= e($status) ?></span></td>
                            <td>
                                <form action="/<?= env('APP_DIR') ?>imports/predict/<?= e($import['id']) ?>" method="POST">
                                    <button class="btn-process" <?= ($status == 'pending') ?: 'disabled' ?>>Run Prediction</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

    <script>
        const fileInput = document.getElementById('file-upload');
        const form = document.getElementById('upload-form');

        // Automatically submit the form when a file is selected
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                // Optional: Show some loading state here
                const btn = document.querySelector('.upload-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = 'Uploading...';
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';

                form.submit();
            }
        });

        // Function to handle running the process
        function runProcess(importId) {
            if (confirm(`Are you sure you want to run the process for ${importId}?`)) {
                // You can replace this with an AJAX call to your backend
                console.log(`Running process for ${importId}`);

                // Example: Send request to backend
                // fetch(`/<?= env('APP_DIR') ?>imports/process/${importId}`, {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //     }
                // })
                // .then(response => response.json())
                // .then(data => {
                //     console.log('Success:', data);
                //     // Reload or update the table
                //     location.reload();
                // })
                // .catch((error) => {
                //     console.error('Error:', error);
                // });
            }
        }
    </script>
</body>

</html>
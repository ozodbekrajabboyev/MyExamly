<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(30, 64, 175, 0.15);
            padding: 3rem;
            width: 100%;
            max-width: 500px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logo-section {
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            padding: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #718096;
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: 2.5rem;
        }

        .input-section {
            margin-bottom: 2rem;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-field {
            width: 100%;
            padding: 1.2rem 1.5rem;
            padding-right: 3.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 500;
            background: #f8fafc;
            transition: all 0.3s ease;
            outline: none;
            text-align: center;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .input-field:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }

        .input-field::placeholder {
            color: #a0aec0;
            letter-spacing: 1px;
            text-transform: none;
        }

        .input-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .input-field:focus + .input-icon {
            color: #3b82f6;
        }

        .submit-btn {
            width: 100%;
            padding: 1.2rem 2rem;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 64, 175, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .submit-btn.loading {
            pointer-events: none;
        }

        .btn-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .message {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 500;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .message.show {
            opacity: 1;
            transform: translateY(0);
        }

        .message.success {
            background: #f0fff4;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .message.error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
        }

        .download-info {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: #e6fffa;
            border: 1px solid #81e6d9;
            border-radius: 12px;
            color: #234e52;
        }

        .download-info.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .footer-text {
            margin-top: 2rem;
            color: #a0aec0;
            font-size: 0.9rem;
        }

        @media (max-width: 640px) {
            .container {
                margin: 1rem;
                padding: 2rem;
            }

            h1 {
                font-size: 1.8rem;
            }

            .subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <div class="logo-icon">
                <img src="{{ asset('logo1.png') }}" alt="Logo">
            </div>
            <h1>Imtihon Natijalari</h1>
            <p class="subtitle">Imtihon kodini kiriting va natijalaringizni ko'ring</p>
        </div>

        <form id="examForm" class="input-section">
            <div class="input-wrapper">
                <input
                    type="text"
                    id="examCode"
                    class="input-field"
                    placeholder="Imtihon kodini kiriting (masalan: 123456)"
                    maxlength="20"
                    autocomplete="off"
                >
                <i class="fas fa-qrcode input-icon"></i>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span class="btn-text">
                    <div class="spinner"></div>
                    <i class="fas fa-download" id="downloadIcon"></i>
                    <span id="btnText">Natijalarni Yuklab Olish</span>
                </span>
            </button>
        </form>

        <div id="message" class="message"></div>
        <div id="downloadInfo" class="download-info"></div>

        <p class="footer-text">
            <i class="fas fa-shield-alt"></i>
            Xavfsiz va ishonchli natijalar portali
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('examForm');
            const examCodeInput = document.getElementById('examCode');
            const submitBtn = document.getElementById('submitBtn');
            const message = document.getElementById('message');
            const downloadInfo = document.getElementById('downloadInfo');
            const spinner = document.querySelector('.spinner');
            const downloadIcon = document.getElementById('downloadIcon');
            const btnText = document.getElementById('btnText');

            // Format input as user types
            examCodeInput.addEventListener('input', function(e) {
                let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                e.target.value = value;
            });

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const examCode = examCodeInput.value.trim();

                if (!examCode) {
                    showMessage('Iltimos, imtihon kodini kiriting', 'error');
                    return;
                }

                if (examCode.length < 3) {
                    showMessage('Imtihon kodi juda qisqa', 'error');
                    return;
                }

                // Show loading state
                setLoadingState(true);
                hideMessages();

                // Simulate API call to validate exam code and download PDF
                setTimeout(() => {
                    validateAndDownload(examCode);
                }, 1500);
            });

            function validateAndDownload(examCode) {
                // Make actual AJAX call to validate exam code against database
                fetch(`/api/exam/${examCode}/validate`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.valid) {
                        // Valid code - trigger download
                        showMessage('✅ Imtihon topildi! PDF yuklanmoqda...', 'success');

                        // Trigger actual PDF download
                        setTimeout(() => {
                            downloadPDF(examCode);
                            setLoadingState(false);
                            btnText.textContent = 'Yana Yuklab Olish';
                        }, 1000);

                    } else {
                        // Invalid code - Reset loading state and show error
                        setLoadingState(false);
                        showMessage('❌ Noto\'g\'ri imtihon kodi. Iltimos, tekshirib qayta urinib ko\'ring.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Validation error:', error);
                    // Always reset loading state on error
                    setLoadingState(false);

                    // Show appropriate error message based on error type
                    if (error.message.includes('Failed to fetch')) {
                        showMessage('❌ Internet aloqasi bilan muammo. Qayta urinib ko\'ring.', 'error');
                    } else if (error.message.includes('HTTP error')) {
                        showMessage('❌ Server bilan aloqa qilishda xatolik. Qayta urinib ko\'ring.', 'error');
                    } else {
                        showMessage('❌ Imtihon kodi tekshirishda xatolik yuz berdi. Qayta urinib ko\'ring.', 'error');
                    }
                })
                .finally(() => {
                    // Ensure loading state is always reset after a delay
                    setTimeout(() => {
                        setLoadingState(false);
                    }, 100);
                });
            }

            function downloadPDF(examCode) {
                // Redirect to your actual Laravel route that generates PDF using dashboard-table.blade.php
                // This will trigger the actual PDF download
                window.location.href = `/exam/${examCode}/download-pdf`;

                // Alternative: You can also use fetch for AJAX download
                // fetch(`/exam/${examCode}/download-pdf`)
                //     .then(response => response.blob())
                //     .then(blob => {
                //         const url = window.URL.createObjectURL(blob);
                //         const a = document.createElement('a');
                //         a.style.display = 'none';
                //         a.href = url;
                //         a.download = `${examCode}_natijalar.pdf`;
                //         document.body.appendChild(a);
                //         a.click();
                //         window.URL.revokeObjectURL(url);
                //     })
                //     .catch(error => {
                //         console.error('Download error:', error);
                //         showMessage('PDF yuklanishida xatolik yuz berdi', 'error');
                //     });
            }

            function setLoadingState(loading) {
                try {
                    if (loading) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                        spinner.style.display = 'block';
                        downloadIcon.style.display = 'none';
                        btnText.textContent = 'Tekshirilmoqda...';
                        examCodeInput.disabled = true;
                    } else {
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                        spinner.style.display = 'none';
                        downloadIcon.style.display = 'block';
                        btnText.textContent = 'Natijalarni Yuklab Olish';
                        examCodeInput.disabled = false;
                    }
                } catch (error) {
                    console.error('Error setting loading state:', error);
                    // Force reset to safe state
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                    spinner.style.display = 'none';
                    downloadIcon.style.display = 'block';
                    btnText.textContent = 'Natijalarni Yuklab Olish';
                    examCodeInput.disabled = false;
                }
            }

            function showMessage(text, type) {
                message.textContent = text;
                message.className = `message ${type} show`;
            }

            function hideMessages() {
                message.classList.remove('show');
                downloadInfo.classList.remove('show');
            }

            // Add some visual feedback for input focus
            examCodeInput.addEventListener('focus', function() {
                this.style.transform = 'scale(1.02)';
            });

            examCodeInput.addEventListener('blur', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>

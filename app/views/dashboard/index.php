<?php
/**
 * Vue du dashboard
 */
$userName = htmlspecialchars($_SESSION['user']['name'] ?? 'Utilisateur');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <link rel="stylesheet" href="assets/css/project-modal.css">
    <style>
        /* ===== STYLES PROFESSIONNELS POUR LES MODALS DE PROJET ===== */
        
        /* Modal container */
        .modal-content {
            border: none;
            border-radius: 24px !important;
            box-shadow: 0 25px 50px rgba(15, 23, 42, 0.2);
            overflow: hidden;
        }
        
        /* Modal header */
        .modal-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 32px 32px 28px;
            border-bottom: none;
            position: relative;
        }
        
        .modal-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 32px;
            width: 60px;
            height: 4px;
            background: #f59e0b;
            border-radius: 2px;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 800;
            color: white;
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .modal-header .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            width: 36px;
            height: 36px;
            opacity: 1;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .modal-header .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        /* Modal body */
        .modal-body {
            padding: 32px !important;
            background: #f8fafc;
        }
        
        /* Form groups */
        .modal-body .mb-3,
        .modal-body .mb-4 {
            background: white;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .modal-body .mb-3:hover,
        .modal-body .mb-4:hover {
            border-color: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }
        
        .modal-body .mb-3:first-child {
            margin-top: 0 !important;
        }
        
        /* Labels */
        .modal-body .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .modal-body .form-label::before {
            content: '';
            width: 4px;
            height: 16px;
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            border-radius: 2px;
        }
        
        /* Form controls */
        .modal-body .form-control,
        .modal-body .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px !important;
            padding: 14px 18px;
            font-size: 15px;
            font-weight: 500;
            color: #1e3a8a;
            transition: all 0.3s ease;
            background: #ffffff;
        }
        
        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: white;
        }
        
        .modal-body .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        
        .modal-body textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        
        /* Submit button */
        .modal-body .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border: none;
            border-radius: 12px !important;
            padding: 16px 32px !important;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            margin-top: 8px;
        }
        
        .modal-body .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
        }
        
        .modal-body .btn-primary:active {
            transform: translateY(0);
        }
        
        /* Icon decorations for inputs */
        .modal-body .mb-3:nth-child(1) .form-label::before {
            background: linear-gradient(135deg, #2563eb, #60a5fa);
        }
        
        .modal-body .mb-3:nth-child(2) .form-label::before {
            background: linear-gradient(135deg, #10b981, #34d399);
        }
        
        .modal-body .mb-3:nth-child(3) .form-label::before {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
        }
        
        .modal-body .mb-3:nth-child(4) .form-label::before {
            background: linear-gradient(135deg, #ec4899, #f472b6);
        }
        
        .modal-body .mb-3:nth-child(5) .form-label::before {
            background: linear-gradient(135deg, #06b6d4, #22d3ee);
        }
        
        .modal-body .mb-3:nth-child(6) .form-label::before {
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        }
        
        .modal-body .mb-4 .form-label::before {
            background: linear-gradient(135deg, #ef4444, #f87171);
        }
        
        /* Input with edit icon */
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon .form-control {
            padding-right: 50px;
        }
        
        .input-edit-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .input-edit-icon:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border-color: #2563eb;
            color: white;
            transform: translateY(-50%) scale(1.1);
        }
        
        .input-with-icon .form-control:focus ~ .input-edit-icon {
            border-color: #2563eb;
        }
        
        /* Animation */
        .modal.fade .modal-dialog {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: scale(0.9) translateY(-20px);
        }
        
        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .modal-header {
                padding: 24px 20px 20px;
            }
            
            .modal-body {
                padding: 24px 20px !important;
            }
            
            .modal-body .mb-3,
            .modal-body .mb-4 {
                padding: 16px;
            }
            
            .modal-title {
                font-size: 20px;
            }
        }
        
        /* Animations et styles dashboard */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .page-header {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .metric-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: scaleIn 0.5s ease-out backwards;
            position: relative;
            overflow: hidden;
        }
        
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        
        /* Variantes de couleurs pour les cards */
        .metric-card-purple::before {
            background: linear-gradient(90deg, #9333ea, #c084fc);
        }
        
        .metric-card-purple .metric-icon {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        }
        
        .metric-card-purple:hover .metric-icon {
            background: linear-gradient(135deg, #c084fc 0%, #a855f7 100%);
        }
        
        .metric-card-purple .metric-icon i {
            color: #7c3aed;
        }
        
        .metric-card-purple:hover .metric-icon i {
            color: white;
        }
        
        .metric-card-purple .metric-value {
            background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .metric-card-purple .metric-action {
            background: #faf5ff;
            color: #6b21a8;
        }
        
        .metric-card-purple .metric-action:hover {
            background: #9333ea;
            color: white;
            border-color: #9333ea;
        }
        
        .metric-card-green::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }
        
        .metric-card-green .metric-icon {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        }
        
        .metric-card-green:hover .metric-icon {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }
        
        .metric-card-green .metric-icon i {
            color: #059669;
        }
        
        .metric-card-green:hover .metric-icon i {
            color: white;
        }
        
        .metric-card-green .metric-value {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .metric-card-green .metric-action {
            background: #f0fdf4;
            color: #065f46;
        }
        
        .metric-card-green .metric-action:hover {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }
        
        .metric-card-orange::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }
        
        .metric-card-orange .metric-icon {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }
        
        .metric-card-orange:hover .metric-icon {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }
        
        .metric-card-orange .metric-icon i {
            color: #d97706;
        }
        
        .metric-card-orange:hover .metric-icon i {
            color: white;
        }
        
        .metric-card-orange .metric-value {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .metric-card-orange .metric-action {
            background: #fffbeb;
            color: #92400e;
        }
        
        .metric-card-orange .metric-action:hover {
            background: #f59e0b;
            color: white;
            border-color: #f59e0b;
        }
        
        .metric-card-pink::before {
            background: linear-gradient(90deg, #ec4899, #f472b6);
        }
        
        .metric-card-pink .metric-icon {
            background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
        }
        
        .metric-card-pink:hover .metric-icon {
            background: linear-gradient(135deg, #f472b6 0%, #ec4899 100%);
        }
        
        .metric-card-pink .metric-icon i {
            color: #db2777;
        }
        
        .metric-card-pink:hover .metric-icon i {
            color: white;
        }
        
        .metric-card-pink .metric-value {
            background: linear-gradient(135deg, #db2777 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .metric-card-pink .metric-action {
            background: #fdf2f8;
            color: #9f1239;
        }
        
        .metric-card-pink .metric-action:hover {
            background: #ec4899;
            color: white;
            border-color: #ec4899;
        }
        
        .metric-card-cyan::before {
            background: linear-gradient(90deg, #06b6d4, #22d3ee);
        }
        
        .metric-card-cyan .metric-icon {
            background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
        }
        
        .metric-card-cyan:hover .metric-icon {
            background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
        }
        
        .metric-card-cyan .metric-icon i {
            color: #0891b2;
        }
        
        .metric-card-cyan:hover .metric-icon i {
            color: white;
        }
        
        .metric-card-cyan .metric-value {
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .metric-card-cyan .metric-action {
            background: #ecfeff;
            color: #164e63;
        }
        
        .metric-card-cyan .metric-action:hover {
            background: #06b6d4;
            color: white;
            border-color: #06b6d4;
        }
        
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        
        .metric-card:hover::before {
            transform: scaleX(1);
        }
        
        .metric-card:nth-child(1) { animation-delay: 0.1s; }
        .metric-card:nth-child(2) { animation-delay: 0.2s; }
        .metric-card:nth-child(3) { animation-delay: 0.3s; }
        .metric-card:nth-child(4) { animation-delay: 0.4s; }
        .metric-card:nth-child(5) { animation-delay: 0.5s; }
        
        .metric-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.15);
            border-color: #93c5fd;
        }
        
        .metric-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }
        
        .metric-card:hover .metric-icon {
            transform: rotate(5deg) scale(1.1);
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        }
        
        .metric-icon i {
            font-size: 24px;
            color: #1e40af;
            transition: color 0.3s ease;
        }
        
        .metric-card:hover .metric-icon i {
            color: white;
        }
        
        .metric-label {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 12px;
        }
        
        .metric-value {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
            line-height: 1;
        }
        
        .metric-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #1e3a8a;
            font-weight: 600;
            text-decoration: none;
            background: #eff6ff;
            padding: 12px 18px;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .metric-action:hover {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
            transform: translateX(4px);
        }
        
        .summary-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 0;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
            animation: slideInRight 0.6s ease-out 0.3s backwards;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .summary-card:hover {
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        }
        
        .summary-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 32px 32px 24px;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border-bottom: 2px solid #f1f5f9;
            position: relative;
        }
        
        .summary-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 32px;
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
        }
        
        .summary-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #bfdbfe;
            transition: all 0.3s ease;
        }
        
        .summary-card:hover .summary-icon {
            transform: rotate(-5deg) scale(1.05);
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border-color: #2563eb;
        }
        
        .summary-icon i {
            color: #2563eb;
            font-size: 24px;
            transition: color 0.3s ease;
        }
        
        .summary-card:hover .summary-icon i {
            color: white;
        }
        
        .summary-title {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: -0.5px;
        }
        
        .summary-text {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Recent reports list - Modern card style */
        .reports-list {
            padding: 24px 32px 32px;
        }
        
        .report-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            margin-bottom: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .report-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #2563eb, #60a5fa);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .report-item:hover::before {
            transform: scaleY(1);
        }
        
        .report-item:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
            transform: translateX(8px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.1);
        }
        
        .report-item:last-child {
            margin-bottom: 0;
        }
        
        /* Report icon with type color */
        .report-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        .report-icon.daily {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }
        
        .report-icon.monthly {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }
        
        .report-icon.annual {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }
        
        .report-item:hover .report-icon.daily {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
        }
        
        .report-item:hover .report-icon.monthly {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .report-item:hover .report-icon.annual {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        /* Report content */
        .report-content {
            flex: 1;
            min-width: 0;
        }
        
        .report-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e3a8a;
            margin: 0 0 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .report-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        
        .report-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }
        
        .report-meta-item i {
            color: #94a3b8;
            font-size: 12px;
        }
        
        /* Report badge */
        .report-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }
        
        .report-badge.daily {
            background: #eff6ff;
            color: #1e40af;
        }
        
        .report-badge.monthly {
            background: #f0fdf4;
            color: #065f46;
        }
        
        .report-badge.annual {
            background: #fffbeb;
            color: #92400e;
        }
        
        /* Empty state */
        .empty-state {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            color: #64748b;
            margin: 24px 32px 32px;
        }
        
        .empty-state i {
            font-size: 56px;
            color: #cbd5e1;
            margin-bottom: 20px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .empty-state strong {
            display: block;
            font-size: 20px;
            margin-bottom: 12px;
            color: #475569;
            font-weight: 700;
        }
        
        .empty-state p {
            color: #64748b;
            font-size: 15px;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include 'app/views/components/sidebar.php'; ?>

        <div class="main-content">
            <header class="page-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-greeting">
                            <i class="fas fa-hand-wave"></i>
                            <span>Bonjour, <?php echo $userName; ?></span>
                        </div>
                        <div class="header-title">
                            <h1>Tableau de bord</h1>
                            <p>Vue d'ensemble de vos chantiers et rapports</p>
                        </div>
                    </div>
                </div>
            </header>

            <section class="stats-grid">
                <div class="metric-card metric-card-purple">
                    <div class="metric-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="metric-label">Projets enregistrés</div>
                    <div class="metric-value"><?php echo $totalProjects; ?></div>
                    <a href="?action=projects" class="metric-action">
                        <span>Voir les projets</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="metric-card metric-card-green">
                    <div class="metric-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="metric-label">Rapports journaliers</div>
                    <div class="metric-value"><?php echo $dailyReports; ?></div>
                    <a href="?action=reports&type=daily" class="metric-action">
                        <span>Voir les rapports</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="metric-card metric-card-orange">
                    <div class="metric-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="metric-label">Rapports mensuels</div>
                    <div class="metric-value"><?php echo $monthlyReports; ?></div>
                    <a href="?action=reports&type=monthly" class="metric-action">
                        <span>Voir les rapports</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="metric-card metric-card-pink">
                    <div class="metric-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="metric-label">Rapports annuels</div>
                    <div class="metric-value"><?php echo $yearlyReports; ?></div>
                    <a href="?action=reports&type=annual" class="metric-action">
                        <span>Voir les rapports</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="metric-card metric-card-cyan">
                    <div class="metric-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="metric-label">Nouveau projet</div>
                    <div class="metric-value">+</div>
                    <button type="button" class="metric-action btn btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#createProjectModalDashboard">
                        <span>Créer un projet</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </section>

            <div class="modal fade" id="createProjectModalDashboard" tabindex="-1" aria-labelledby="createProjectModalDashboardLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createProjectModalDashboardLabel">
                                <i class="fas fa-plus-circle me-2"></i>
                                Enregistrer un projet
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="?action=projects/handle-create">
                                <input type="hidden" name="return_to" value="dashboard">
                                <div class="mb-3">
                                    <label for="dashboard_name" class="form-label">Nom du projet</label>
                                    <input type="text" class="form-control" id="dashboard_name" name="name" placeholder="Ex: Construction du pont de la rivière" required>
                                </div>
                                <div class="mb-3">
                                    <label for="dashboard_location" class="form-label">Localisation</label>
                                    <input type="text" class="form-control" id="dashboard_location" name="location" placeholder="Ex: Kinshasa, RDC" required>
                                </div>
                                <div class="mb-3">
                                    <label for="dashboard_project_type" class="form-label">Type de projet</label>
                                    <div class="input-with-icon">
                                        <input type="text" class="form-control" id="dashboard_project_type_display" placeholder="Sélectionnez ou saisissez un type" readonly>
                                        <input type="hidden" id="dashboard_project_type" name="project_type" required>
                                        <div class="input-edit-icon" id="editDashboardProjectTypeBtn" title="Modifier le type">
                                            <i class="fas fa-pencil-alt"></i>
                                        </div>
                                    </div>
                                    <div id="dashboardProjectTypeOptions" style="display: none; margin-top: 12px;">
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary dashboard-project-type-option" data-value="batiment">🏢 Bâtiment</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary dashboard-project-type-option" data-value="route">🛣️ Route</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary dashboard-project-type-option" data-value="pont">🌉 Pont</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary dashboard-project-type-option" data-value="château d'eau">💧 Château d'eau</button>
                                        </div>
                                        <div class="mt-2">
                                            <input type="text" class="form-control form-control-sm" id="dashboardCustomProjectType" placeholder="Ou saisissez un type personnalisé...">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="dashboard_maitre_ouvrage" class="form-label">Maître d'ouvrage</label>
                                    <input type="text" class="form-control" id="dashboard_maitre_ouvrage" name="maitre_ouvrage" placeholder="Ex: Ministère des Infrastructures">
                                </div>
                                <div class="mb-3">
                                    <label for="dashboard_missions_controle" class="form-label">Missions de contrôle</label>
                                    <textarea class="form-control" id="dashboard_missions_controle" name="missions_controle" rows="3" placeholder="Décrivez les missions de contrôle..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="dashboard_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="dashboard_description" name="description" rows="4" placeholder="Décrivez les objectifs et caractéristiques du projet..."></textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="dashboard_start_date" class="form-label">Date d'enregistrement</label>
                                    <input type="date" class="form-control" id="dashboard_start_date" name="start_date" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Enregistrer le projet
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <section class="summary-card">
                <div class="summary-header">
                    <div class="summary-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h2 class="summary-title">Rapports récents</h2>
                        <p class="summary-text">Suivez l'activité de vos derniers rapports générés</p>
                    </div>
                </div>
                <?php if (!empty($recentReports)): ?>
                    <div class="reports-list">
                        <?php foreach ($recentReports as $report): ?>
                            <?php 
                                $reportType = strtolower($report['report_type']);
                                $typeIcons = [
                                    'daily' => 'D',
                                    'monthly' => 'M',
                                    'annual' => 'A'
                                ];
                                $typeLabels = [
                                    'daily' => 'Journalier',
                                    'monthly' => 'Mensuel',
                                    'annual' => 'Annuel'
                                ];
                            ?>
                            <div class="report-item">
                                <div class="report-icon <?php echo $reportType; ?>">
                                    <?php echo $typeIcons[$reportType] ?? 'R'; ?>
                                </div>
                                <div class="report-content">
                                    <h3 class="report-title"><?php echo htmlspecialchars($report['title']); ?></h3>
                                    <div class="report-meta">
                                        <span class="report-meta-item">
                                            <i class="fas fa-folder"></i>
                                            <?php echo htmlspecialchars($report['project_name']); ?>
                                        </span>
                                        <span class="report-meta-item">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($report['created_at'])); ?>
                                        </span>
                                        <span class="report-meta-item">
                                            <i class="fas fa-clock"></i>
                                            <?php echo date('H:i', strtotime($report['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <span class="report-badge <?php echo $reportType; ?>">
                                    <?php echo $typeLabels[$reportType] ?? ucfirst($reportType); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-file-invoice"></i>
                        <strong>Aucun rapport généré</strong>
                        <p>Commencez par créer un projet pour générer vos premiers rapports !</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ===== GESTION DU TYPE DE PROJET AVEC ICÔNE D'ÉDITION (DASHBOARD) =====
            const editDashboardProjectTypeBtn = document.getElementById('editDashboardProjectTypeBtn');
            const dashboardProjectTypeOptions = document.getElementById('dashboardProjectTypeOptions');
            const dashboardProjectTypeDisplay = document.getElementById('dashboard_project_type_display');
            const dashboardProjectTypeHidden = document.getElementById('dashboard_project_type');
            const dashboardCustomProjectType = document.getElementById('dashboardCustomProjectType');
            const dashboardProjectTypeOptionBtns = document.querySelectorAll('.dashboard-project-type-option');
            
            const typeLabels = {
                'batiment': '🏢 Bâtiment',
                'route': '🛣️ Route',
                'pont': '🌉 Pont',
                'château d\'eau': '💧 Château d\'eau'
            };
            
            if (editDashboardProjectTypeBtn) {
                editDashboardProjectTypeBtn.addEventListener('click', function() {
                    dashboardProjectTypeOptions.style.display = dashboardProjectTypeOptions.style.display === 'none' ? 'block' : 'none';
                });
                
                dashboardProjectTypeOptionBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const value = this.dataset.value;
                        dashboardProjectTypeHidden.value = value;
                        dashboardProjectTypeDisplay.value = typeLabels[value] || value;
                        dashboardProjectTypeOptions.style.display = 'none';
                        dashboardCustomProjectType.value = '';
                    });
                });
                
                dashboardCustomProjectType.addEventListener('input', function() {
                    if (this.value.trim()) {
                        dashboardProjectTypeHidden.value = this.value.trim();
                        dashboardProjectTypeDisplay.value = this.value.trim();
                    }
                });
                
                dashboardCustomProjectType.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        dashboardProjectTypeOptions.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>

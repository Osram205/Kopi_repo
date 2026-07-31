<style>
    /* Fallback definition for Toyota Type */
    @font-face {
        font-family: 'Toyota Type';
        src: local('Toyota Type'), local('ToyotaType');
        font-weight: 400;
        font-style: normal;
    }
    @font-face {
        font-family: 'Toyota Type';
        src: local('Toyota Type Bold'), local('ToyotaType-Bold');
        font-weight: 700;
        font-style: normal;
    }

    :root {
        /* Yellow / Red / Black Premium Palette */
        --kopi-ink: #09090B;          /* Jet Black */
        --kopi-surface: var(--color-brand-800);      /* Onyx for Cards/Containers */
        --kopi-line: #27272A;         /* Borders / Dividers */
        --kopi-muted: #A1A1AA;        /* Zinc 400 for secondary text */
        
        --kopi-primary: var(--color-primary-500);      /* Golden Yellow (Primary Action) */
        --kopi-primary-hover: #F59E0B;
        
        --kopi-accent: #DC2626;       /* Crimson Red (Alerts, highlights) */
        --kopi-accent-hover: #B91C1C;
        
        --kopi-text: #FAFAFA;         /* Smoke White for readability */
        --kopi-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    body {
        min-height: 100vh;
        color: var(--kopi-text) !important;
        background: var(--kopi-ink) !important; 
        font-family: "Toyota Type", "ToyotaType", Inter, ui-sans-serif, system-ui, sans-serif !important;
    }

    .kopi-shell {
        min-height: calc(100vh - 74px);
    }

    .kopi-navbar {
        background: rgba(9, 9, 11, .90) !important;
        backdrop-filter: blur(14px);
        border-bottom: 1px solid rgba(255, 255, 255, .05) !important;
    }

    .kopi-brand-mark {
        display: inline-flex;
        width: 2.5rem;
        height: 2.5rem;
        align-items: center;
        justify-content: center;
        border-radius: .85rem;
        background: var(--kopi-primary);
        border: none;
        box-shadow: 0 10px 20px rgba(251, 191, 36, .20);
        overflow: hidden;
    }

    .kopi-brand-mark img {
        width: 86%;
        height: 86%;
        object-fit: contain;
    }

    .navbar .nav-link {
        border-radius: .7rem;
        color: rgba(250, 250, 250, .70) !important;
        font-weight: 700;
        padding: .55rem .8rem;
        transition: all 0.2s ease;
    }

    .navbar .nav-link:hover,
    .navbar .nav-link:focus {
        color: var(--kopi-primary) !important;
        background: rgba(251, 191, 36, .10);
    }

    /* Cards & Containers */
    .kopi-card,
    .card {
        background-color: var(--kopi-surface) !important;
        border: 1px solid var(--kopi-line) !important;
        border-radius: 1rem !important;
        box-shadow: var(--kopi-shadow) !important;
        color: var(--kopi-text) !important;
    }

    .card-header, .card-footer {
        background-color: transparent !important;
        border-color: var(--kopi-line) !important;
    }

    /* Override Bootstrap Utility Classes for Dark Mode */
    .bg-white { background-color: var(--kopi-surface) !important; }
    .bg-light { background-color: var(--kopi-line) !important; }
    .text-dark { color: var(--kopi-text) !important; }
    .text-success { color: var(--kopi-primary) !important; } /* Price in yellow */
    .text-secondary, .text-muted { color: var(--kopi-muted) !important; }
    
    .card-title, .card-text, p, h1, h2, h3, h4, h5, h6 {
        color: var(--kopi-text) !important;
    }

    /* Buttons */
    .btn {
        border-radius: .75rem;
        font-weight: 750;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--kopi-primary) !important;
        border-color: var(--kopi-primary) !important;
        color: #09090B !important; /* High contrast */
    }

    .btn-primary:hover,
    .btn-primary:focus {
        background: var(--kopi-primary-hover) !important;
        border-color: var(--kopi-primary-hover) !important;
        transform: scale(0.98);
    }
    
    .btn-dark {
        background: #FAFAFA !important;
        border-color: #FAFAFA !important;
        color: #09090B !important;
    }
    .btn-dark:hover {
        background: #E4E4E7 !important;
        transform: scale(0.98);
    }

    .btn-danger {
        background: var(--kopi-accent) !important;
        border-color: var(--kopi-accent) !important;
        color: #FFF !important;
    }
    
    .btn-outline-primary {
        color: var(--kopi-primary) !important;
        border-color: var(--kopi-primary) !important;
    }
    .btn-outline-primary:hover {
        background: var(--kopi-primary) !important;
        color: #09090B !important;
    }
    
    .btn-outline-dark {
        color: var(--kopi-text) !important;
        border-color: var(--kopi-line) !important;
    }
    .btn-outline-dark:hover {
        background: var(--kopi-line) !important;
    }

    /* Form Inputs */
    .form-control,
    .form-select,
    .input-group-text {
        background-color: #27272A !important;
        color: #FAFAFA !important;
        border: 1px solid var(--kopi-line) !important;
        border-radius: .75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--kopi-primary) !important;
        box-shadow: 0 0 0 .25rem rgba(251, 191, 36, .15) !important;
        background-color: #3f3f46 !important;
    }
    
    ::placeholder {
        color: #71717A !important;
    }
    
    /* Fix for Bootstrap form-floating labels in dark mode */
    .form-floating label {
        color: var(--kopi-muted) !important;
    }
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: var(--kopi-primary) !important;
        background-color: transparent !important;
        transform: scale(0.85) translateY(-1.2rem) translateX(0.15rem);
    }
    .form-floating > .form-control:focus, .form-floating > .form-control:not(:placeholder-shown) {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
    .form-floating > .form-control:-webkit-autofill {
        -webkit-text-fill-color: var(--kopi-text);
        -webkit-box-shadow: 0 0 0px 1000px #27272A inset;
    }

    .badge {
        font-weight: 750;
    }
    
    .bg-primary {
        background-color: var(--kopi-primary) !important;
        color: #09090B !important;
    }
    .text-primary {
        color: var(--kopi-primary) !important;
    }
    
    .badge.bg-warning {
        background-color: var(--kopi-primary) !important;
        color: #09090B !important;
    }

    /* Typography */
    .tracking-wider {
        letter-spacing: 0;
    }

    .kopi-page-title {
        font-weight: 850;
        letter-spacing: -0.5px;
    }

    /* Auth Screens specific */
    .kopi-auth {
        min-height: 100vh;
        background:
            radial-gradient(circle at 18% 20%, rgba(251, 191, 36, .08), transparent 20rem),
            linear-gradient(135deg, #09090b 0%, var(--color-brand-800) 100%);
    }

    .kopi-auth-card {
        border: 1px solid rgba(255, 255, 255, .08) !important;
        background: rgba(24, 24, 27, .80) !important;
        backdrop-filter: blur(20px);
    }

    .kopi-auth-kicker {
        color: var(--kopi-primary);
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    /* Utilities */
    .animate-pulse {
        animation: kopiPulse 1.4s ease-in-out infinite;
    }

    @keyframes kopiPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .68; }
    }
    
    /* Override table text colors if used */
    .table {
        color: var(--kopi-text) !important;
    }
    
    /* Modals */
    .modal-content {
        background-color: var(--kopi-surface) !important;
        border: 1px solid var(--kopi-line) !important;
        color: var(--kopi-text);
    }
    .modal-header {
        border-bottom-color: var(--kopi-line) !important;
    }
    .modal-footer {
        border-top-color: var(--kopi-line) !important;
    }

    @media (max-width: 575.98px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .btn-lg {
            font-size: 1rem;
        }
    }
</style>

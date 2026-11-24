<style>
    /* =========================================================
       Halaman autentikasi (login & register)
       ========================================================= */
    .auth-layout {
        --auth-accent: #2563eb;
        --auth-accent-soft: rgba(37, 99, 235, 0.12);
        --auth-gold: #fbbf24;
        --auth-gold-soft: #fcd34d;
        --auth-emerald: #10b981;
        --auth-emerald-soft: #34d399;
        min-height: 100vh;
        min-height: 100dvh;
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(0, 0.92fr);
        background-color: var(--ds-body-bg);
        color: var(--ds-body-color);
    }

    /* ---------- Panel visual kiri ---------- */
    .auth-aside {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: clamp(1.25rem, 2.2vh, 1.75rem);
        padding: clamp(1.5rem, 2.8vw, 2.75rem);
        color: #f8fafc;
        background:
            radial-gradient(95% 78% at 114% -20%, rgba(125, 211, 252, 0.52), transparent 58%),
            radial-gradient(85% 75% at -18% 118%, rgba(8, 18, 48, 0.6), transparent 62%),
            linear-gradient(162deg, #0a1d4a 0%, #153070 45%, #312e81 100%);
    }

    /* Layer latar belakang premium: mesh gradient + orb gradasi + grid halus */
    .auth-aside-bg {
        position: absolute;
        inset: 0;
        z-index: -1;
        pointer-events: none;
        overflow: hidden;
    }

    .auth-mesh-gradient {
        position: absolute;
        inset: -20%;
        background:
            radial-gradient(ellipse 600px 400px at 20% 15%, rgba(37, 99, 235, 0.25), transparent 55%),
            radial-gradient(ellipse 500px 350px at 85% 30%, rgba(16, 185, 129, 0.18), transparent 60%),
            radial-gradient(ellipse 450px 450px at 50% 85%, rgba(251, 191, 36, 0.15), transparent 50%);
        filter: blur(60px);
        opacity: 0.6;
        animation: authMeshShift 18s ease-in-out infinite;
    }

    .auth-gradient-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.45;
        animation: authOrbFloat 14s ease-in-out infinite;
    }

    .auth-gradient-orb--top {
        width: 480px;
        height: 480px;
        top: -140px;
        right: -120px;
        background: radial-gradient(circle at 30% 30%, rgba(191, 219, 254, 0.8), rgba(37, 99, 235, 0.25) 55%, transparent 75%);
        animation-delay: 0s;
    }

    .auth-gradient-orb--bottom {
        width: 400px;
        height: 400px;
        bottom: -100px;
        left: -80px;
        background: radial-gradient(circle at 70% 70%, rgba(251, 191, 36, 0.55), rgba(217, 119, 6, 0.18) 55%, transparent 75%);
        animation-delay: -7s;
    }

    .auth-grid-pattern {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.015) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
        background-size: 56px 56px;
        mask-image: linear-gradient(to bottom, #000 0%, rgba(0, 0, 0, 0.35) 45%, transparent 90%);
        -webkit-mask-image: linear-gradient(to bottom, #000 0%, rgba(0, 0, 0, 0.35) 45%, transparent 90%);
    }

    /* Garis buku kas: tekstur halus seperti lembar pencatatan premium. */
    .auth-aside::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        background-image: repeating-linear-gradient(to bottom,
                rgba(255, 255, 255, 0.035) 0 1px,
                transparent 1px 40px);
        -webkit-mask-image: linear-gradient(to bottom, #000 0%, rgba(0, 0, 0, 0.4) 55%, transparent 92%);
        mask-image: linear-gradient(to bottom, #000 0%, rgba(0, 0, 0, 0.4) 55%, transparent 92%);
        pointer-events: none;
    }

    /* Dekorasi sudut halus untuk keseimbangan visual */
    .auth-aside-decor {
        position: absolute;
        inset: 0;
        z-index: -1;
        pointer-events: none;
        background:
            radial-gradient(ellipse 200px 200px at 0% 0%, rgba(191, 219, 254, 0.12), transparent 70%),
            radial-gradient(ellipse 240px 240px at 100% 100%, rgba(251, 191, 36, 0.08), transparent 75%);
    }

    .auth-brand {
        display: inline-flex;
        width: fit-content;
        border-radius: 18px;
        text-decoration: none;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .auth-brand:hover,
    .auth-brand:focus-visible {
        transform: translateY(-3px);
        outline: none;
        box-shadow: 0 24px 48px -18px rgba(2, 6, 23, 0.65);
    }

    .auth-brand-plaque {
        display: block;
        width: clamp(120px, 10vw, 144px);
        padding: 0.5rem 0.65rem 0.55rem;
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
        box-shadow: 0 16px 32px -12px rgba(2, 6, 23, 0.55), inset 0 1px 0 #ffffff;
    }

    .auth-brand-plaque img {
        display: block;
        width: 100%;
        height: auto;
    }

    .auth-aside-body {
        display: flex;
        flex-direction: column;
        gap: clamp(1rem, 1.8vh, 1.5rem);
        width: min(100%, 520px);
        margin-block: auto;
    }

    .auth-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        width: fit-content;
        padding: 0.3rem 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.09);
        color: #ffffff;
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        backdrop-filter: blur(8px);
    }

    .auth-eyebrow .ti {
        color: var(--auth-gold-soft);
        font-size: 0.7rem;
    }

    .auth-headline {
        margin: 0.5rem 0 0.35rem;
        color: #ffffff;
        font-size: clamp(1.8rem, 2.3vw, 2.5rem);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.025em;
        text-wrap: balance;
    }

    .auth-accent-text {
        color: var(--auth-gold-soft);
        background: linear-gradient(135deg, var(--auth-gold-soft) 0%, #ffffff 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .auth-lead {
        max-width: 42ch;
        margin: 0;
        color: rgba(255, 255, 255, 0.84);
        font-size: 0.88rem;
        line-height: 1.65;
        font-weight: 400;
    }

    /* Statistik social proof */
    .auth-stats {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.25rem;
        margin-top: 0.25rem;
        padding: 1.1rem 0.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .auth-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.2rem;
        text-align: center;
        flex: 1;
        min-width: 0;
    }

    .auth-stat-value {
        color: #ffffff;
        font-size: clamp(1.25rem, 1.8vw, 1.5rem);
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.02em;
        white-space: nowrap;
    }

    .auth-stat-label {
        color: rgba(255, 255, 255, 0.62);
        font-size: 0.65rem;
        font-weight: 500;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .auth-stat-divider {
        width: 1px;
        height: 2.5rem;
        background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.18), transparent);
    }

    /* Badge profesional */
    .auth-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        width: fit-content;
        padding: 0.45rem 0.9rem;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.07);
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        backdrop-filter: blur(10px);
        margin-inline: auto;
    }

    .auth-badge .ti {
        color: var(--auth-gold-soft);
        font-size: 0.85rem;
    }

    .auth-points {
        display: grid;
        gap: 0.3rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .auth-points li {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        padding: 0.45rem 0;
        color: rgba(255, 255, 255, 0.94);
        font-size: 0.78rem;
        line-height: 1.55;
        transition: color 0.2s ease;
    }

    .auth-points li:hover {
        color: #ffffff;
    }

    .auth-points li:first-child {
        padding-top: 0;
    }

    .auth-points li:last-child {
        padding-bottom: 0;
    }

    .auth-point-icon {
        flex: 0 0 22px;
        width: 22px;
        height: 22px;
        display: grid;
        place-items: center;
        margin-top: 0.1rem;
        border: 1px solid rgba(252, 211, 77, 0.3);
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.15), rgba(251, 191, 36, 0.05));
        color: var(--auth-gold-soft);
        font-size: 0.7rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .auth-points li:hover .auth-point-icon {
        transform: scale(1.08);
        box-shadow: 0 4px 12px -2px rgba(251, 191, 36, 0.35);
    }

    .auth-stage {
        position: relative;
        width: auto;
        aspect-ratio: 1;
        height: clamp(250px, 36vh, 380px);
        max-width: 100%;
        margin: 0 auto;
    }

    .auth-stage-glow {
        position: absolute;
        inset: -5%;
        border-radius: 50%;
        background:
            radial-gradient(circle at 50% 46%, rgba(191, 219, 254, 0.4), rgba(37, 99, 235, 0.18) 40%, transparent 68%);
        pointer-events: none;
        animation: authGlowPulse 4s ease-in-out infinite;
    }

    .auth-stage-ring {
        position: absolute;
        inset: -2.5%;
        border-radius: 50%;
        border: 1px solid rgba(191, 219, 254, 0.12);
        pointer-events: none;
    }

    .auth-stage-accent {
        position: absolute;
        inset: -3.5%;
        border-radius: 50%;
        border: 1px solid transparent;
        background: linear-gradient(135deg, rgba(191, 219, 254, 0.25), transparent 40%, rgba(16, 185, 129, 0.15)) border-box;
        -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0) border-box;
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
        opacity: 0.6;
        animation: authAccentRotate 20s linear infinite;
    }

    .auth-art {
        position: absolute;
        inset: 0;
        z-index: 1;
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 24px 48px rgba(2, 6, 23, 0.4));
        animation: authFloat 8s ease-in-out infinite;
    }

    .auth-aside-foot {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-top: 0.85rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.73rem;
        line-height: 1.5;
    }

    .auth-aside-foot .ti {
        flex: 0 0 auto;
        font-size: 0.95rem;
        color: var(--auth-emerald-soft);
    }

    /* ---------- Panel form kanan ---------- */
    .auth-main {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(1.5rem, 4vw, 3.5rem);
    }

    .auth-panel {
        width: 100%;
        max-width: 430px;
        margin-inline: auto;
        animation: authRise 0.45s ease-out both;
    }

    .auth-theme-toggle {
        position: absolute;
        z-index: 3;
        top: 1.15rem;
        right: 1.15rem;
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        border: 1px solid var(--ds-border-color);
        border-radius: 12px;
        background: var(--ds-body-bg);
        color: var(--ds-secondary-color);
        font-size: 1.05rem;
        cursor: pointer;
        transition: color 0.18s ease, border-color 0.18s ease, background-color 0.18s ease;
    }

    .auth-theme-toggle:hover,
    .auth-theme-toggle:focus-visible {
        border-color: rgba(37, 99, 235, 0.4);
        background: var(--auth-accent-soft);
        color: var(--auth-accent);
        outline: none;
    }

    .auth-mobile-brand {
        display: none;
        width: 138px;
        margin-bottom: 1.4rem;
        padding: 0.42rem 0.55rem 0.48rem;
        border: 1px solid var(--ds-border-color);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 12px 26px -14px rgba(15, 23, 42, 0.45);
        text-decoration: none;
        transition: transform 0.2s ease;
    }

    .auth-mobile-brand:hover,
    .auth-mobile-brand:focus-visible {
        transform: translateY(-1px);
        outline: none;
    }

    .auth-mobile-brand img {
        display: block;
        width: 100%;
        height: auto;
    }

    .auth-heading {
        margin-bottom: 1.6rem;
    }

    .auth-heading .auth-eyebrow {
        margin-bottom: 0.85rem;
        border-color: rgba(37, 99, 235, 0.18);
        background: var(--auth-accent-soft);
        color: var(--auth-accent);
        letter-spacing: 0.08em;
        backdrop-filter: none;
    }

    .auth-heading h1 {
        margin: 0 0 0.45rem;
        color: var(--ds-emphasis-color, var(--ds-body-color));
        font-size: clamp(1.6rem, 2.2vw, 2rem);
        font-weight: 800;
        letter-spacing: -0.035em;
        line-height: 1.15;
    }

    .auth-heading p {
        margin: 0;
        color: var(--ds-secondary-color);
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .auth-alert {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.2rem;
        padding: 0.8rem 0.95rem;
        border-radius: 0.85rem;
        font-size: 0.86rem;
    }

    .auth-alert .ti {
        font-size: 1.15rem;
    }

    .auth-field {
        margin-bottom: 1rem;
    }

    .auth-field .form-label {
        margin-bottom: 0.4rem;
        color: var(--ds-body-color);
        font-size: 0.83rem;
        font-weight: 600;
    }

    .auth-control {
        position: relative;
    }

    .auth-control-icon {
        position: absolute;
        z-index: 2;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        color: var(--ds-secondary-color);
        font-size: 1.05rem;
        pointer-events: none;
        transition: color 0.18s ease;
    }

    .auth-control .form-control {
        height: 3.2rem;
        padding-right: 2.85rem;
        padding-left: 2.85rem;
        border: 1.5px solid var(--ds-border-color);
        border-radius: 0.85rem;
        background-color: var(--ds-tertiary-bg);
        color: var(--ds-body-color);
        font-size: 0.93rem;
        transition: border-color 0.18s ease, background-color 0.18s ease, box-shadow 0.18s ease;
    }

    .auth-control .form-control::placeholder {
        color: var(--ds-secondary-color);
        opacity: 0.75;
    }

    .auth-control .form-control:hover:not(:focus):not(.is-invalid) {
        border-color: rgba(37, 99, 235, 0.35);
    }

    .auth-control .form-control:focus {
        border-color: #3b82f6;
        background-color: var(--ds-body-bg);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.13);
    }

    .auth-control .form-control.is-invalid {
        border-color: var(--ds-danger);
        background-image: none;
    }

    .auth-control .form-control.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
    }

    .auth-control:focus-within .auth-control-icon {
        color: var(--auth-accent);
    }

    .auth-password-toggle {
        position: absolute;
        z-index: 3;
        top: 50%;
        right: 0.45rem;
        width: 2.35rem;
        height: 2.35rem;
        display: grid;
        place-items: center;
        transform: translateY(-50%);
        border: 0;
        border-radius: 0.6rem;
        background: transparent;
        color: var(--ds-secondary-color);
        font-size: 1rem;
        cursor: pointer;
        transition: color 0.15s ease, background-color 0.15s ease;
    }

    .auth-password-toggle:hover,
    .auth-password-toggle:focus-visible {
        background: var(--auth-accent-soft);
        color: var(--auth-accent);
        outline: none;
    }

    .auth-error {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.4rem;
        color: var(--ds-danger);
        font-size: 0.8rem;
        line-height: 1.4;
    }

    .auth-note {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        margin: 0.25rem 0 1.25rem;
        padding: 0.8rem 0.95rem;
        border: 1px solid rgba(37, 99, 235, 0.18);
        border-radius: 0.85rem;
        background: rgba(37, 99, 235, 0.07);
        color: var(--ds-secondary-color);
        font-size: 0.79rem;
        line-height: 1.5;
    }

    .auth-note .ti {
        flex: 0 0 auto;
        color: var(--auth-accent);
        font-size: 1.15rem;
        line-height: 1.2;
    }

    .auth-submit {
        height: 3.2rem;
        border: 0;
        border-radius: 0.85rem;
        background: linear-gradient(135deg, #2563eb, #4338ca);
        box-shadow: 0 14px 26px -10px rgba(37, 99, 235, 0.65);
        color: #ffffff;
        font-size: 0.94rem;
        font-weight: 700;
        transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
    }

    .auth-submit:hover:not(:disabled),
    .auth-submit:focus-visible:not(:disabled) {
        background: linear-gradient(135deg, #1d4ed8, #3730a3);
        box-shadow: 0 18px 30px -10px rgba(37, 99, 235, 0.7);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .auth-submit:active:not(:disabled) {
        transform: translateY(0);
    }

    .auth-submit:disabled {
        box-shadow: none;
        opacity: 0.78;
    }

    .auth-submit .ti {
        transition: transform 0.18s ease;
    }

    .auth-submit:hover:not(:disabled) .ti {
        transform: translateX(3px);
    }

    .auth-divider {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin: 1.35rem 0;
        color: var(--ds-secondary-color);
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .auth-divider::before,
    .auth-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--ds-border-color);
    }

    .auth-alt-btn {
        height: 3rem;
        border: 1.5px solid var(--ds-border-color);
        border-radius: 0.85rem;
        background: var(--ds-body-bg);
        color: var(--ds-body-color);
        font-size: 0.9rem;
        font-weight: 600;
        transition: color 0.18s ease, border-color 0.18s ease, background-color 0.18s ease;
    }

    .auth-alt-btn:hover,
    .auth-alt-btn:focus-visible {
        border-color: rgba(37, 99, 235, 0.4);
        background: var(--auth-accent-soft);
        color: var(--auth-accent);
    }

    .auth-footnote {
        margin: 1.5rem 0 0;
        color: var(--ds-secondary-color);
        font-size: 0.79rem;
        line-height: 1.5;
        text-align: center;
    }

    .auth-strength {
        display: grid;
        gap: 0.35rem;
        margin-top: 0.5rem;
    }

    .auth-strength-track {
        height: 5px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(145, 158, 171, 0.28);
    }

    .auth-strength-bar {
        display: block;
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: var(--ds-danger);
        transition: width 0.25s ease, background-color 0.25s ease;
    }

    .auth-strength-text {
        color: var(--ds-secondary-color);
        font-size: 0.71rem;
        line-height: 1.4;
    }

    @keyframes authRise {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes authFloat {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-9px);
        }
    }

    @keyframes authOrbFloat {

        0%,
        100% {
            transform: translate(0, 0) scale(1);
        }

        33% {
            transform: translate(20px, -15px) scale(1.02);
        }

        66% {
            transform: translate(-15px, 20px) scale(0.98);
        }
    }

    @keyframes authMeshShift {

        0%,
        100% {
            transform: translate(0, 0) scale(1) rotate(0deg);
        }

        25% {
            transform: translate(-30px, 20px) scale(1.03) rotate(1deg);
        }

        50% {
            transform: translate(20px, -30px) scale(0.97) rotate(-1deg);
        }

        75% {
            transform: translate(-20px, -20px) scale(1.02) rotate(0.5deg);
        }
    }

    @keyframes authGlowPulse {

        0%,
        100% {
            opacity: 0.6;
            transform: scale(1);
        }

        50% {
            opacity: 0.85;
            transform: scale(1.03);
        }
    }

    @keyframes authAccentRotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* ---------- Responsif ---------- */
    @media (max-width: 1199.98px) {
        .auth-aside {
            padding: 1.75rem;
            gap: clamp(1rem, 2vh, 1.5rem);
        }

        .auth-headline {
            font-size: clamp(1.65rem, 2.6vw, 2rem);
        }

        .auth-stats {
            gap: 1rem;
            padding: 0.9rem 0.5rem;
        }

        .auth-stat-value {
            font-size: clamp(1.15rem, 1.6vw, 1.35rem);
        }

        .auth-stat-label {
            font-size: 0.62rem;
        }

        .auth-stat-divider {
            height: 2.2rem;
        }

        .auth-badge {
            padding: 0.4rem 0.75rem;
            font-size: 0.7rem;
        }

        .auth-points li {
            gap: 0.6rem;
            padding: 0.45rem 0;
            font-size: 0.75rem;
        }
    }

    @media (min-width: 992px) {
        .auth-aside {
            position: sticky;
            top: 0;
            height: 100vh;
            height: 100dvh;
        }
    }

    @media (min-width: 992px) and (max-height: 900px) {
        .auth-aside {
            gap: 0.85rem;
            padding-block: 1.5rem;
        }

        .auth-brand-plaque {
            width: 120px;
        }

        .auth-headline {
            font-size: 1.8rem;
        }

        .auth-lead {
            font-size: 0.83rem;
        }

        .auth-stats {
            gap: 1rem;
            padding: 0.85rem 0.5rem;
        }

        .auth-stat-value {
            font-size: 1.2rem;
        }

        .auth-stat-label {
            font-size: 0.6rem;
        }

        .auth-stat-divider {
            height: 2rem;
        }

        .auth-badge {
            padding: 0.35rem 0.7rem;
            font-size: 0.68rem;
        }

        .auth-stage {
            height: clamp(240px, 36vh, 340px);
        }

        .auth-points li {
            gap: 0.55rem;
            padding: 0.4rem 0;
            font-size: 0.72rem;
        }

        .auth-aside-foot {
            padding-top: 0.65rem;
            font-size: 0.7rem;
        }
    }

    @media (min-width: 992px) and (max-height: 800px) {
        .auth-lead,
        .auth-aside-foot,
        .auth-stats {
            display: none;
        }

        .auth-badge {
            display: none;
        }

        .auth-main {
            padding-block: 1.5rem;
        }

        .auth-brand-plaque {
            width: 112px;
        }

        .auth-headline {
            margin-block: 0.5rem 0.25rem;
            font-size: 1.65rem;
        }

        .auth-stage {
            height: clamp(200px, 36vh, 300px);
        }
    }

    @media (min-width: 992px) and (max-height: 700px) {
        .auth-main {
            padding-block: 1rem;
        }

        .auth-heading {
            margin-bottom: 1rem;
        }

        .auth-heading p {
            font-size: 0.85rem;
        }

        .auth-note {
            margin: 0.2rem 0 0.9rem;
            padding: 0.6rem 0.8rem;
        }

        .auth-divider {
            margin: 0.9rem 0;
        }

        .auth-footnote {
            margin-top: 1rem;
            font-size: 0.74rem;
        }
    }

    @media (min-width: 992px) and (max-height: 620px) {
        .auth-points {
            display: none;
        }

        .auth-brand-plaque {
            width: 104px;
        }

        .auth-stage {
            height: clamp(170px, 40vh, 240px);
        }
    }

    @media (max-width: 991.98px) {
        .auth-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .auth-aside {
            display: none;
        }

        .auth-main {
            min-height: 100vh;
            min-height: 100dvh;
            align-items: flex-start;
            padding: 1.75rem 1.25rem 2.5rem;
        }

        .auth-panel {
            max-width: 440px;
        }

        .auth-mobile-brand {
            display: block;
        }

        .auth-theme-toggle {
            top: 1rem;
            right: 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .auth-main {
            padding: 1.15rem 1rem 1.25rem;
        }

        .auth-heading h1 {
            font-size: 1.55rem;
        }

        .auth-note {
            font-size: 0.76rem;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .auth-panel,
        .auth-art {
            animation: none;
        }

        .auth-submit,
        .auth-submit .ti {
            transition: none;
        }
    }
</style>

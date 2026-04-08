<template>
    <footer class="footer">
        <div class="footer-overlay">
            <div class="footer-container">
                <div class="footer-top">
                    <router-link to="/" class="footer-logo-link">
                        <img
                            src="/images/logo.png"
                            alt="LocalEvents"
                            class="footer-logo"
                            onerror="this.style.display='none'"
                        />
                        <span class="footer-logo-text">LocalEvents</span>
                    </router-link>

                    <p class="footer-claim">Explorando experiencias únicas cerca de ti</p>

                    <div class="footer-socials">
                        <a href="#" aria-label="Facebook" class="social-pill">f</a>
                        <a href="#" aria-label="Twitter" class="social-pill">t</a>
                        <a href="#" aria-label="Instagram" class="social-pill">◎</a>
                        <a href="#" aria-label="YouTube" class="social-pill">▶</a>
                    </div>
                </div>

                <div class="footer-divider"></div>

                <div class="footer-bottom">
                    <p class="footer-copy">© 2026 LocalEvents. Todos los derechos reservados.</p>

                    <div class="footer-legal">
                        <router-link to="/legal/aviso-legal">Aviso legal</router-link>
                        <router-link to="/legal/privacidad">Política de privacidad</router-link>
                        <router-link to="/legal/cookies">Política de cookies</router-link>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
// Componente footer global del sitio
</script>

<style scoped>
.footer {
    margin-top: auto;
    background-image: url('/images/footer-bg.jpg');
    background-size: cover;
    background-position: center;
    color: white;
}

.footer-overlay {
    background: linear-gradient(
        to bottom,
        rgba(74, 18, 92, 0.45),
        rgba(32, 8, 49, 0.7)
    );
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 56px 24px 24px;
    text-align: center;
}

.footer-top {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 18px;
}

.footer-logo-link {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

.footer-logo {
    height: 52px;
    width: auto;
    object-fit: contain;
}

.footer-logo-text {
    font-size: 2rem;
    font-weight: 800;
    color: white;
}

.footer-claim {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 500;
}

.footer-socials {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 8px;
}

.social-pill {
    width: 42px;
    height: 42px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.18);
    color: white;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    transition: transform 0.2s ease, background 0.2s ease;
}

.social-pill:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.28);
}

.footer-divider {
    width: 100%;
    height: 1px;
    background: rgba(255, 255, 255, 0.35);
    margin: 28px 0 20px;
}

.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.footer-copy {
    margin: 0;
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.92);
}

.footer-legal {
    display: flex;
    gap: 18px;
    flex-wrap: wrap;
    justify-content: center;
}

.footer-legal a {
    color: rgba(255, 255, 255, 0.92);
    text-decoration: none;
    font-size: 0.95rem;
    transition: opacity 0.2s ease;
}

.footer-legal a:hover {
    opacity: 0.8;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .footer-logo-text {
        font-size: 1.6rem;
    }

    .footer-claim {
        font-size: 1.05rem;
    }

    .footer-bottom {
        flex-direction: column;
        text-align: center;
    }

    .footer-legal {
        gap: 12px;
    }
}
</style>

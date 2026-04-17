<template>
  <div class="home-page">

    <!-- HERO -->
    <section class="hero">
      <div class="hero-overlay">
        <div class="container hero-content">
          <h1>Descubre los mejores<br />eventos en tu ciudad</h1>
          <p>Explora y reserva actividades únicas cerca de ti</p>

          <form class="hero-search" @submit.prevent="searchEvents">
            <span class="search-icon left">
              <svg viewBox="0 0 24 24" fill="none">
                <path
                  d="M21 21L16.65 16.65M10.5 18C6.35786 18 3 14.6421 3 10.5C3 6.35786 6.35786 3 10.5 3C14.6421 3 18 6.35786 18 10.5C18 14.6421 14.6421 18 10.5 18Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </span>

            <input
              v-model="search"
              type="text"
              placeholder="Buscar eventos..."
            />

            <button type="submit" class="search-button" aria-label="Buscar">
              <svg viewBox="0 0 24 24" fill="none">
                <path
                  d="M21 21L16.65 16.65M10.5 18C6.35786 18 3 14.6421 3 10.5C3 6.35786 6.35786 3 10.5 3C14.6421 3 18 6.35786 18 10.5C18 14.6421 14.6421 18 10.5 18Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </button>
          </form>
        </div>
      </div>
    </section>

    <!-- EVENTOS POPULARES -->
    <section class="popular-events section">
      <div class="container">
        <h2 class="section-title">Eventos populares</h2>

        <div class="cards-grid">
          <article
            v-for="event in popularEvents"
            :key="event.id"
            class="event-card"
          >
            <div class="event-image-wrap">
              <img :src="event.image" :alt="event.title" class="event-image" />
            </div>

            <div class="event-body">
              <h3>{{ event.title }}</h3>
              <p class="location">{{ event.location }}</p>
              <p class="price">Desde {{ event.price }}</p>
              <Button @click="handleReservarHome(event.url)" label="Reservar" severity="primary" class="btn-reservar" />
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- CATEGORÍAS -->
    <section class="categories section">
      <div class="container">
        <h2 class="section-title">Eventos por categoría</h2>

        <div class="categories-grid">
          <button
            v-for="category in categories"
            :key="category.id"
            class="category-card"
            @click="searchByCategory(category.id)"
            :aria-label="'Buscar ' + category.name"
          >
            <div class="category-icon-wrap">
              <img :src="category.icon" :alt="category.name" class="category-icon" />
            </div>
          </button>
        </div>

        <div class="categories-cta">
          <Button as="router-link" to="/categorias" label="Ver todas las categorías" severity="primary" class="btn-reservar" />
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Button from 'primevue/button'
import { authStore } from '@/store/auth'
import { useRouter } from 'vue-router'

const search = ref('')
const popularEvents = ref([])
const categories = ref([])
const isLoading = ref(false)
const auth = authStore()
const router = useRouter()

const fetchData = async () => {
  isLoading.value = true
  try {
    // Traemos los eventos mas recientes o populares (por ahora los 3 ultimos)
    const eventosRes = await axios.get('/api/eventos', {
      params: { order_column: 'created_at', order_direction: 'desc' }
    })
    
    // Mapeamos los datos de la API al formato que espera el diseño
    popularEvents.value = (eventosRes.data?.data || eventosRes.data).slice(0, 3).map(e => ({
      id: e.id_evento,
      title: e.nombre,
      location: e.localizacion || 'Sin ubicación',
      price: e.precio ? `${e.precio}€` : 'Gratis',
      image: e.imagen || '/images/eventomuestra.webp',
      url: `/eventos/${e.id_evento}`
    }))

    // Traemos las categorias
    const catRes = await axios.get('/api/category-list')
    categories.value = (catRes.data?.data || catRes.data).slice(0, 4).map(c => ({
      id: c.id_categoria,
      name: c.nombre,
      // Como las categorias no tienen icono en la DB, usamos uno por defecto segun el nombre
      icon: getCategoryIcon(c.nombre)
    }))
  } catch (err) {
    console.error('Error cargando datos de la home:', err)
  } finally {
    isLoading.value = false
  }
}

// Funcion para asignar iconos/fotos a las categorias si no vienen de la DB
const getCategoryIcon = (name) => {
  const normalized = name ? name.toLowerCase() : '';
  if (normalized.includes('concierto')) return '/images/conciertos.png';
  if (normalized.includes('teatro')) return '/images/teatro.png';
  if (normalized.includes('gastronom')) return '/images/gastronomia.png';
  if (normalized.includes('exposicion')) return '/images/exposiciones.png';
  
  return '/images/eventomuestra.webp'
}

onMounted(fetchData)

const searchEvents = () => {
  const query = search.value.trim()
  if (!query) {
    router.push({ name: 'public.buscar-eventos' })
    return
  }
  router.push({ name: 'public.buscar-eventos', query: { q: query } })
}

const searchByCategory = (categoryId) => {
  router.push({ name: 'public.buscar-eventos', query: { categoria: categoryId } })
}

const handleReservarHome = (url) => {
  if (!auth.authenticated) {
    router.push({ name: 'public.login' })
  } else {
    router.push(url)
  }
}
</script>

<style scoped>
:root {
  --primary-blue: #2e5bb7;
  --primary-green: #4fd0b4;
  --primary-yellow: #f2b23f;
  --text-dark: #1a1a1a;
  --text-soft: #4b4b4b;
  --bg-light: #f7f7f7;
  --white: #ffffff;
}

* {
  box-sizing: border-box;
}

.home-page {
  background: #ffffff;
  color: var(--text-dark);
  font-family: Arial, Helvetica, sans-serif;
}

.container {
  width: min(1180px, calc(100% - 48px));
  margin: 0 auto;
}

.section {
  padding: 56px 0;
}

.section-title {
  margin: 0 0 28px;
  font-size: 2.25rem;
  font-weight: 800;
  line-height: 1.1;
  color: #111111;
}

/* HEADER */
.home-header {
  background: #ffffff;
  border-bottom: 1px solid #ececec;
  position: sticky;
  top: 0;
  z-index: 20;
}

.header-inner {
  min-height: 88px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
}

.logo-wrap {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
}

.logo {
  height: 150px;
  width: auto;
  object-fit: contain;
}

.main-nav {
  display: flex;
  align-items: center;
  gap: 40px;
}

.main-nav a {
  color: var(--primary-blue);
  font-size: 1rem;
  font-weight: 700;
  text-decoration: none;
}

.main-nav a:hover {
  opacity: 0.85;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 14px;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: 0.2s ease;
  font-weight: 700;
}

.btn:hover {
  transform: translateY(-1px);
}

.btn-primary {
  background: var(--primary-blue);
  color: #ffffff;
  min-width: 150px;
  padding: 12px 22px;
  border-radius: 14px;
  box-shadow: 0 3px 0 rgba(0, 0, 0, 0.08);
}

.btn-secondary {
  background: var(--primary-green);
  color: #ffffff;
  min-width: 150px;
  padding: 12px 22px;
  border-radius: 14px;
  box-shadow: 0 3px 0 rgba(0, 0, 0, 0.08);
}

/* HERO */
.hero {
  min-height: 560px;
  background-image: url('/images/Foto_banner.png');
  background-size: cover;
  background-position: center;
  position: relative;
}

.hero-overlay {
  background: linear-gradient(
    to bottom,
    rgba(35, 32, 32, 0.18),
    rgba(35, 32, 32, 0.22)
  );
  min-height: 560px;
}

.hero-content {
  min-height: 560px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 40px 0;
}

.hero h1 {
  margin: 0;
  color: var(--primary-blue);
  font-size: clamp(2.3rem, 5vw, 4.3rem);
  line-height: 1.02;
  font-weight: 900;
  letter-spacing: 0.2px;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.12);
}

.hero p {
  margin: 22px 0 28px;
  font-size: 1.35rem;
  font-weight: 700;
  color: #22488e;
}

.hero-search {
  width: min(860px, 100%);
  height: 72px;
  background: #ffffff;
  border-radius: 18px;
  display: flex;
  align-items: center;
  overflow: hidden;
  box-shadow: 0 8px 26px rgba(0, 0, 0, 0.12);
}

.hero-search .left {
  width: 78px;
  min-width: 78px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary-blue);
}

.hero-search .left svg,
.search-button svg {
  width: 34px;
  height: 34px;
}

.hero-search input {
  flex: 1;
  height: 100%;
  border: none;
  outline: none;
  font-size: 1.15rem;
  font-weight: 600;
  color: #26447a;
  padding: 0 10px 0 0;
}

.hero-search input::placeholder {
  color: #32518f;
  opacity: 0.9;
}

.search-button {
  width: 120px;
  min-width: 120px;
  height: 100%;
  border: none;
  background: var(--primary-yellow);
  color: var(--primary-blue);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

/* POPULAR EVENTS */
.popular-events {
  background: #ffffff;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 38px;
}

.event-card {
  background: #ffffff;
  display: flex;
  flex-direction: column;
}

.event-image-wrap {
  width: 100%;
  aspect-ratio: 1 / 0.76;
  overflow: hidden;
  background: #efefef;
}

.event-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}

.event-body {
  padding: 12px 0 0;
  display: flex;
  flex-direction: column;
}

.event-body h3 {
  margin: 0 0 10px;
  white-space: pre-line;
  font-size: 1.9rem;
  line-height: 1.05;
  font-weight: 900;
  color: #171717;
}

.event-body .location {
  margin: 0 0 8px;
  font-size: 1.22rem;
  color: #2d2d2d;
}

.event-body .price {
  margin: 0 0 18px;
  font-size: 1.45rem;
  font-weight: 800;
  color: #222222;
}

:deep(.btn-reservar) {
  font-size: 1.25rem;
  font-weight: 700;
  padding: 12px 32px !important;
}

:deep(.btn-reservar .p-button-label) {
  font-weight: 700;
}

/* CATEGORIES */
.categories {
  background: #ffffff;
  padding-top: 8px;
  padding-bottom: 72px;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 26px;
  align-items: start;
}

.category-card {
  text-align: center;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
  transition: transform 0.2s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.category-card:hover {
  transform: translateY(-4px);
}

.category-icon-wrap {
  background: #f4f4f4;
  border-radius: 20px;
  padding: 0;
  margin-bottom: 14px;
  width: 100%;
  aspect-ratio: 1;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.category-icon {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.category-card:hover .category-icon {
  transform: scale(1.05);
}

.category-title {
  font-size: 1.4rem;
  font-weight: 800;
  color: #171717;
  margin: 0;
}

.categories-cta {
  display: flex;
  justify-content: center;
  margin-top: 32px;
}

.btn-wide {
  background: var(--primary-green);
  color: #ffffff;
  min-width: 430px;
  padding: 14px 30px;
  border-radius: 14px;
  font-size: 1.3rem;
  box-shadow: 0 3px 0 rgba(0, 0, 0, 0.08);
}

/* RESPONSIVE */
@media (max-width: 1100px) {
  .cards-grid,
  .categories-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .hero h1 {
    font-size: clamp(2.1rem, 5vw, 3.4rem);
  }
}

@media (max-width: 820px) {
  .header-inner {
    flex-direction: column;
    justify-content: center;
    padding: 18px 0;
  }

  .main-nav {
    gap: 24px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .header-actions {
    flex-wrap: wrap;
    justify-content: center;
  }

  .hero,
  .hero-overlay,
  .hero-content {
    min-height: 470px;
  }

  .hero-search {
    height: 62px;
  }

  .search-button {
    width: 92px;
    min-width: 92px;
  }
}

@media (max-width: 640px) {
  .container {
    width: min(100% - 28px, 100%);
  }

  .section {
    padding: 42px 0;
  }

  .section-title {
    font-size: 1.9rem;
  }

  .cards-grid,
  .categories-grid {
    grid-template-columns: 1fr;
  }

  .hero h1 {
    font-size: 2rem;
  }

  .hero p {
    font-size: 1rem;
  }

  .hero-search {
    height: 56px;
  }

  .hero-search .left {
    width: 56px;
    min-width: 56px;
  }

  .hero-search .left svg,
  .search-button svg {
    width: 26px;
    height: 26px;
  }

  .btn-primary,
  .btn-secondary,
  .btn-wide,
  .btn-card {
    min-width: unset;
    width: 100%;
  }

  .event-body h3 {
    font-size: 1.55rem;
  }

  .category-card h3 {
    font-size: 1.5rem;
  }
}
</style>
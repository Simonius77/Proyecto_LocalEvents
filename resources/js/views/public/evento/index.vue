<template>
  <div class="search-page section">
    <div class="container">
      <div class="search-header">
        <h1 class="page-title">
          {{ searchCategoria ? 'Buscando por categoría' : (searchQuery ? 'Resultados de búsqueda' : 'Explorar todos los eventos') }}
        </h1>
        
        <form class="search-bar" @submit.prevent="executeSearch">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Introduce el nombre de un evento..."
          />
          <button type="submit" class="search-button">Buscar</button>
        </form>
      </div>

      <div v-if="isLoading" class="loading-state">
        <i class="pi pi-spinner pi-spin text-4xl text-primary-500"></i>
        <p>Cargando eventos...</p>
      </div>
      
      <div v-else-if="events.length === 0" class="empty-state">
        <i class="pi pi-search" style="font-size: 3rem; color: #ccc;"></i>
        <p>No se encontraron eventos para esta búsqueda.</p>
        <button @click="clearSearch" class="btn-clear mt-4">Ver todos los eventos</button>
      </div>

      <div v-else class="cards-grid">
        <article
          v-for="event in events"
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

            <Button @click="handleReservar(event.url)" label="Reservar" severity="primary" class="btn-reservar" />
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import Button from 'primevue/button'
import { authStore } from '@/store/auth'

const route = useRoute()
const router = useRouter()
const auth = authStore()

const events = ref([])
const isLoading = ref(false)
const searchQuery = ref('')
const searchCategoria = ref('')

const fetchEvents = async () => {
  isLoading.value = true
  try {
    const params = {}
    if (searchQuery.value) params.search_global = searchQuery.value
    if (searchCategoria.value) params.search_categoria = searchCategoria.value

    const res = await axios.get('/api/eventos', { params })
    const data = res.data?.data || res.data

    events.value = data.map(e => ({
      id: e.id_evento,
      title: e.nombre,
      location: e.localizacion || 'Sin ubicación',
      price: e.precio ? `${e.precio}€` : 'Gratis',
      image: e.imagen || '/images/eventomuestra.webp',
      url: `/eventos/${e.id_evento}`
    }))
  } catch (err) {
    console.error('Error fetching searched events:', err)
  } finally {
    isLoading.value = false
  }
}

const executeSearch = () => {
  // Clear category when searching via text to expand results safely
  searchCategoria.value = ''
  router.push({ name: 'public.buscar-eventos', query: { q: searchQuery.value } })
}

const clearSearch = () => {
  searchQuery.value = ''
  searchCategoria.value = ''
  router.push({ name: 'public.buscar-eventos' })
}

const handleReservar = (url) => {
  if (!auth.authenticated) {
    router.push({ name: 'public.login' })
  } else {
    router.push(url)
  }
}

watch(() => route.query, (newQuery) => {
  searchQuery.value = newQuery.q || ''
  searchCategoria.value = newQuery.categoria || ''
  fetchEvents()
})

onMounted(() => {
  searchQuery.value = route.query.q || ''
  searchCategoria.value = route.query.categoria || ''
  fetchEvents()
})
</script>

<style scoped>
:root {
  --primary-blue: #2e5bb7;
  --primary-green: #4fd0b4;
  --primary-yellow: #f2b23f;
  --text-dark: #1a1a1a;
}

.search-page {
  background: #ffffff;
  color: #1a1a1a;
  min-height: 70vh;
  font-family: Arial, Helvetica, sans-serif;
  padding: 60px 0;
}

.container {
  width: min(1180px, calc(100% - 48px));
  margin: 0 auto;
}

.search-header {
  margin-bottom: 50px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.page-title {
  font-size: 2.8rem;
  font-weight: 900;
  margin-bottom: 30px;
}

.search-bar {
  width: min(700px, 100%);
  height: 64px;
  background: #ffffff;
  border: 1px solid #e0e0e0;
  border-radius: 14px;
  display: flex;
  align-items: center;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

.search-bar input {
  flex: 1;
  height: 100%;
  border: none;
  outline: none;
  font-size: 1.15rem;
  color: #333;
  padding: 0 20px;
}

.search-button {
  width: 120px;
  height: 100%;
  border: none;
  background: var(--primary-blue, #2e5bb7);
  color: #ffffff;
  font-weight: bold;
  font-size: 1.1rem;
  cursor: pointer;
  transition: opacity 0.2s;
}

.search-button:hover {
  opacity: 0.9;
}

.loading-state, .empty-state {
  text-align: center;
  padding: 80px 0;
  color: #666;
}

.empty-state p {
  margin-top: 20px;
  font-size: 1.2rem;
}

.btn-clear {
  margin-top: 15px;
  padding: 10px 20px;
  background: #f4f4f4;
  border: 1px solid #ddd;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  color: #333;
}

.btn-clear:hover {
  background: #eee;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 38px;
}

/* EVENT CARDS REUSED DIRECTLY FROM HOME DESIGN */
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
  border-radius: 12px;
}

.event-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.event-body {
  padding: 18px 0 0;
  display: flex;
  flex-direction: column;
}

.event-body h3 {
  margin: 0 0 10px;
  font-size: 1.7rem;
  line-height: 1.1;
  font-weight: 900;
  color: #171717;
}

.event-body .location {
  margin: 0 0 8px;
  font-size: 1.1rem;
  color: #333;
}

.event-body .price {
  margin: 0 0 18px;
  font-size: 1.3rem;
  font-weight: 800;
  color: #222;
}

:deep(.btn-reservar) {
  font-size: 1.15rem;
  font-weight: 700;
  padding: 10px 24px !important;
}

@media (max-width: 1024px) {
  .cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .cards-grid {
    grid-template-columns: 1fr;
  }
  .page-title {
    font-size: 2rem;
  }
}
</style>

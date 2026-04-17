<template>
  <div class="categorias-page">
    <div class="container">
      <h1 class="page-title">Descubre eventos por categoría</h1>
      <p class="page-subtitle">Selecciona una categoría para ver todos los eventos disponibles y empieza tu próxima aventura.</p>

      <div v-if="isLoading" class="loading-state">
        <i class="pi pi-spinner pi-spin text-4xl text-primary-500"></i>
        <p>Cargando categorías...</p>
      </div>

      <div v-else class="categories-grid">
        <button
          v-for="category in categories"
          :key="category.id"
          class="category-card"
          @click="viewCategoryEvents(category.id)"
          :aria-label="'Ver eventos de ' + category.name"
        >
          <div class="category-icon-wrap">
            <img :src="category.icon" :alt="category.name" class="category-icon" />
          </div>
          <!-- Not adding h3 to match the home page design where texts are in the images -->
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const categories = ref([])
const isLoading = ref(false)

const getCategoryIcon = (name) => {
  const normalized = name ? name.toLowerCase() : '';
  if (normalized.includes('concierto')) return '/images/conciertos.png';
  if (normalized.includes('teatro')) return '/images/teatro.png';
  if (normalized.includes('gastronom')) return '/images/gastronomia.png';
  if (normalized.includes('exposicion')) return '/images/exposiciones.png';
  
  return '/images/eventomuestra.webp'
}

const fetchData = async () => {
  isLoading.value = true
  try {
    const catRes = await axios.get('/api/category-list')
    categories.value = (catRes.data?.data || catRes.data).map(c => ({
      id: c.id_categoria,
      name: c.nombre,
      icon: getCategoryIcon(c.nombre)
    }))
  } catch (err) {
    console.error('Error cargando categorías:', err)
  } finally {
    isLoading.value = false
  }
}

const viewCategoryEvents = (id) => {
  router.push({ name: 'public.buscar-eventos', query: { categoria: id } })
}

onMounted(fetchData)
</script>

<style scoped>
:root {
  --primary-blue: #2e5bb7;
  --text-dark: #1a1a1a;
}

.categorias-page {
  background: #ffffff;
  color: #1a1a1a;
  min-height: 70vh;
  padding: 60px 0;
  font-family: Arial, Helvetica, sans-serif;
}

.container {
  width: min(1180px, calc(100% - 48px));
  margin: 0 auto;
}

.page-title {
  font-size: 2.8rem;
  font-weight: 900;
  color: #111;
  margin-bottom: 15px;
  text-align: center;
}

.page-subtitle {
  font-size: 1.2rem;
  color: #4b4b4b;
  text-align: center;
  margin-bottom: 60px;
}

.loading-state {
  text-align: center;
  padding: 60px 0;
  color: #666;
}

.loading-state p {
  margin-top: 15px;
  font-weight: 600;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
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

/* RESPONSIVE */
@media (max-width: 1100px) {
  .categories-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 820px) {
  .categories-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .page-title {
    font-size: 2.2rem;
  }
}

@media (max-width: 640px) {
  .categories-grid {
    grid-template-columns: 1fr;
  }
  .page-title {
    font-size: 1.8rem;
  }
}
</style>

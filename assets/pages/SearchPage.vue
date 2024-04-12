<script setup>
import { onMounted, ref } from 'vue';
import { fetchCardBySearch } from '../services/cardService';

const cards = ref([]);
const loadingCard = ref(true);
async function loadingCards() {
    loadingCard.value = true;
    cards.value = await fetchCardBySearch('car');
    loadingCard.value = false;
}

onMounted(() => {
    loadingCards();
});

</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
    </div>
    <div class="card-list">
        <div>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

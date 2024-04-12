<script setup>
import {onMounted, ref} from 'vue';
import {fetchAllCards, fetchSetCodes} from '../services/cardService';

const cards = ref([]);
const setCodes = ref([]);
const selectedSetCode = ref('');
const loadingCards = ref(true);
const page = ref(1);

async function loadCards() {
    loadingCards.value = true;
    cards.value = await fetchAllCards();
    loadingCards.value = false;
}

async function loadSetCodes() {
    setCodes.value = await fetchSetCodes();
}

onMounted(() => {
    loadCards();
    loadSetCodes();
});

</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
        <select v-model="selectedSetCode" @change="loadCards">
            <option value="">All</option>
            <option v-for="setCode in setCodes" :key="setCode" :value="setCode">{{ setCode }}
            </option>
        </select>
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
            <button @click="page++ && loadCards()">Next Page</button>
        </div>
    </div>
</template>

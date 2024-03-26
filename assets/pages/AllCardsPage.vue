<script setup>
import { onMounted, ref, watch } from 'vue';
import { fetchAllCards, fetchAllSetCode } from '../services/cardService';

const cards = ref([]);
const setCodes = ref([]);
const selected = ref("");
const loadingCards = ref(true);
const error = ref(false);
const errorMessage = ref("");

async function loadCards() {
    loadingCards.value = true;
    cards.value = await fetchAllCards(selected.value);
    loadingCards.value = false;
}

async function loadsetCodes() {
    try {
        setCodes.value = await fetchAllSetCode();
    } catch (err) {
        error.value = true;
        errorMessage.value = err;
    }
}

onMounted(() => {
    loadsetCodes();
    loadCards();
});

watch(selected, () => {
    loadCards();
});

</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
    </div>
    <div class="card-list">
        <select v-model="selected">
            <option value="">...</option>
            <option v-for="option in setCodes" :value="option">
                {{ option }}
            </option>
        </select>

        <div v-if="error">
            Une erreur s'est produite : <br />
            {{ errorMessage }}
        </div>

        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

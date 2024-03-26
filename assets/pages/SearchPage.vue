<script setup>
import { ref, watch, onMounted } from 'vue';
import { fetchCardBySearch, fetchAllSetCode } from '../services/cardService';

const searchQuery = ref("");
const cards = ref([]);
const setCodes = ref([]);
const selected = ref("");
const setCodeStatus = ref({});
const loadingCards = ref(false);
const error = ref(false);
const errorMessage = ref("");

async function loadCards() {
    if(3 <= searchQuery.value.length){
        error.value = false;
        loadingCards.value = true;
        
        try {
            cards.value = await fetchCardBySearch(searchQuery.value, selected.value);
        } catch (err) {
            error.value = true;
            errorMessage.value = err;
        }
        
        loadingCards.value = false; 
    }
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
});

watch([searchQuery, selected], () => {
    loadCards();
});

watch(cards, (newCards) => {
    const newSetCodeStatus = {};
    setCodes.value.forEach((setCode) => {
        newSetCodeStatus[setCode] = newCards.some(card => card.setCode === setCode);
    });
    setCodeStatus.value = newSetCodeStatus;
}, { deep: true });
</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
    </div>
    <div class="card-list" id="search-card-list">   
        <input type="text" placeholder="Search" v-model="searchQuery"/>

        <select v-model="selected">
            <option value="">...</option>
            <option v-for="option in setCodes" 
                :value="option" 
                v-bind:class="(Object.keys(setCodeStatus).length > 0 && !setCodeStatus[option]) 
                    ? 'notInCards' 
                    : 'isInCards'
                "
            >
                {{ option }}
            </option>
        </select>

        <div v-if="error">
            Une erreur s'est produite : <br />
            {{ errorMessage }}
        </div>
        
        <div v-if="loadingCards">Loading...</div>
        <div v-else>  
            <div class="card" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }"> {{ card.name }} - {{ card.uuid }} </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { fetchCardBySearch } from '../services/cardService';

const searchQuery = ref("");
const cards = ref([]);
const loadingCards = ref(false);
const error = ref(false);
const errorMessage = ref("");

async function loadCards() {
    if(3 <= searchQuery.value.length){
        error.value = false;
        loadingCards.value = true;
        
        try {
            cards.value = await fetchCardBySearch(searchQuery.value);
        } catch (err) {
            error.value = true;
            errorMessage.value = err;
        }
        
        loadingCards.value = false; 
    }
}

watch(searchQuery, () => {
    loadCards();
});

</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
    </div>
    <div class="card-list">   
        <input type="text" placeholder="Search" v-model="searchQuery"/>

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

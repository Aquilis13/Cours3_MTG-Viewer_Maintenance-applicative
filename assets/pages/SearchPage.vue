<script>
// TODO: La page de recheche de cartes.
import { onMounted, ref } from 'vue';
import { fetchCardBySearch } from '../services/cardService';

export default {
    data(){
        return {
            searchQuery: "",
            cards: [],
            loadingCards: false,
            error: false,
            errorMessage: ""
        }
    },
    methods:{
        loadCards() {
            if(3 <= this.searchQuery.length){
                this.error = false;
                this.loadingCards = true;
                
                fetchCardBySearch(this.searchQuery)
                .then(cards => {
                    this.cards = cards;
                })
                .catch(error => {
                    this.error = true;
                    this.errorMessage = error;
                });
                
                this.loadingCards = false; 
            }
        }
    }
}
</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
    </div>
    <div class="card-list">   
        <input type="text" placeholder="Search" v-model="searchQuery" @input="loadCards"/>

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

<style>
input {
    border-radius: 13px;
    width: 400px;
    height: 25px;
    border: 1px solid gray;
    margin-bottom: 40px;
    padding-left: 13px;
    font-family: sans-serif;
}
</style>
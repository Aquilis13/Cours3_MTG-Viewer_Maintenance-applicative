<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchAllCards, fetchAllSetCode } from '../services/cardService';

const route = useRoute();
const cards = ref([]);
const nbCards = ref(0);
const setCodes = ref([]);
const selected = ref("");
const page = ref(1);
const nbPages = ref(0);

const loadingCards = ref(true);
const error = ref(false);
const errorMessage = ref("");

async function loadCards() {
    loadingCards.value = true;

    const datas = await fetchAllCards(page.value, selected.value);
    cards.value = datas.data;
    nbCards.value = datas.nb_result;
    nbPages.value = Math.ceil(nbCards.value / 100);

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

async function changePage(newPage) {
    page.value = newPage;
}

async function previousPage() {
    if(page.value > 1){
        page.value--;
    }
}

async function nextPage() {
    if(page.value < nbPages.value){
        page.value++;
    }
}

onMounted(() => {
    if(route.params.setCode){
        selected.value = route.params.setCode;
    }

    loadsetCodes();
    loadCards();
});

watch([selected, page], () => {
    loadCards();
});

</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
    </div>
    <div class="card-list">
        <div class="filter">
            <div>
                <label>Set codes</label><br />
                <select v-model="selected">
                    <option value="">--- Set codes ---</option>
                    <option v-for="option in setCodes" :value="option">
                        {{ option }}
                    </option>
                </select>
            </div>
            
            <div>
                <label>Pages</label><br />
                <select v-model="page">
                    <option value="">--- Pages ---</option>
                    <option v-for="option in Array.from({ length: nbPages }, (v, k) => k+1)" :key="option">
                        {{ option }}
                    </option>
                </select>
            </div>
        </div>

        <div v-if="error">
            Une erreur s'est produite : <br />
            {{ errorMessage }}
        </div>

        <div v-if="loadingCards">Loading...</div>
        <div v-else>

            <div class="pagination">
                <button @click="previousPage()"><<</button>
                <span v-if="nbPages > 10">
                    <button :class="page == 1 ? 'selected' : ''" @click="changePage(1)">1</button>
                    <button v-if="page > 2" @click="changePage(page -1)">{{ page -1}}</button>
                    <button v-if="page > 1" @click="changePage(page)" class="selected">{{ page }}</button>

                    <span v-for="n in Array.from({ length: 9 }, (v, k) => k+1)" :key="n">
                        <button v-if="page < nbPages" @click="changePage(n + page)">{{ n + page }}</button>
                    </span>

                    <button v-if="page != nbPages" @click="changePage(nbPages)">{{ nbPages }}</button>
                </span>
                <span v-else>
                    <span v-for="n in Array.from({ length: nbPages }, (v, k) => k+1)" :key="n">
                        <button :class="page == n ? 'selected' : ''" @click="changePage(n)">{{ n }}</button>
                    </span>
                </span>

                <button @click="nextPage()">>></button>
            </div>

            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

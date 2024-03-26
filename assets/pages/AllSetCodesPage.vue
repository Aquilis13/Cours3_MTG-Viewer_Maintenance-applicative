<script setup>
import { onMounted, ref, watch } from 'vue';
import { fetchAllCards, fetchAllSetCode } from '../services/cardService';

const setCodes = ref([]);
const loadingSetCodes = ref(true);
const error = ref(false);
const errorMessage = ref("");

async function loadsetCodes() {
    loadingSetCodes.value = true;
    try {
        setCodes.value = await fetchAllSetCode();
    } catch (err) {
        error.value = true;
        errorMessage.value = err;
    }
    loadingSetCodes.value = false;
}

onMounted(() => {
    loadsetCodes();
});
</script>

<template>
    <div>
        <h1>Tous les set-codes</h1>
    </div>
    <div class="set-codes-list">
        <div v-if="error">
            Une erreur s'est produite : <br />
            {{ errorMessage }}
        </div>

        <div v-if="loadingSetCodes">Loading...</div>
        <div v-else>
            <ul>
                <li class="set-codes-result" v-for="setCode in setCodes">
                    <router-link :to="{ name: 'cards-by-set-code', params: { setCode: setCode } }">
                        {{ setCode }}
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>

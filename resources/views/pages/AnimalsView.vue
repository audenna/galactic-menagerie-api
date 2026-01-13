<template>
    <div class="animals-view">
        <h1>Animal Management</h1>

        <!-- Tabs -->
        <div class="tabs">
            <button :class="{ active: activeTab === 'create' }" @click="activeTab = 'create'">Create Animal</button>
            <button :class="{ active: activeTab === 'transfer' }" @click="activeTab = 'transfer'">Transfer Animal</button>
        </div>

        <!-- Create Animal Form -->
        <div v-if="activeTab === 'create'" class="tab-content">
            <h2>Create Animal</h2>

            <div v-if="createSuccess" class="alert alert-success">{{ createSuccess }}</div>
            <div v-if="createError" class="alert alert-danger">{{ createError }}</div>

            <form @submit.prevent="createAnimal">
                <div>
                    <label>Name:</label>
                    <input v-model="createForm.name" type="text" required />
                </div>

                <div>
                    <label>Species:</label>
                    <input v-model="createForm.species" type="text" required />
                </div>

                <div>
                    <label>Preferred Environment:</label>
                    <input v-model="createForm.preferred_environment" type="text" required />
                </div>

                <div>
                    <label>Enclosure ID:</label>
                    <input v-model.number="createForm.enclosure_id" type="number" min="1" required />
                </div>

                <button type="submit">Create</button>
            </form>
        </div>

        <!-- Transfer Animal Form -->
        <div v-if="activeTab === 'transfer'" class="tab-content">
            <h2>Transfer Animal</h2>

            <div v-if="transferSuccess" class="alert alert-success">{{ transferSuccess }}</div>
            <div v-if="transferError" class="alert alert-danger">{{ transferError }}</div>

            <form @submit.prevent="transferAnimal">
                <div>
                    <label>Animal ID:</label>
                    <input v-model.number="transferForm.animal_id" type="number" min="1" required />
                </div>

                <div>
                    <label>Target Enclosure ID:</label>
                    <input v-model.number="transferForm.target_enclosure_id" type="number" min="1" required />
                </div>

                <button type="submit">Transfer</button>
            </form>
        </div>

        <!-- Animals List -->
        <h3>Animals</h3>
        <ul>
            <li v-for="animal in animals" :key="animal.id">
                {{ animal.id }} — {{ animal.name }} ({{ animal.species }}) — Enclosure: {{ animal.enclosure_id }}
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Tabs
const activeTab = ref('create');

// Animals list
const animals = ref([]);

// Create form
const createForm = ref({
    name: '',
    species: '',
    preferred_environment: '',
    enclosure_id: null,
});
const createSuccess = ref('');
const createError = ref('');

// Transfer form
const transferForm = ref({
    animal_id: null,
    target_enclosure_id: null,
});
const transferSuccess = ref('');
const transferError = ref('');

// Fetch all animals
const fetchAnimals = async () => {
    try {
        const response = await axios.get('/api/v1/animals');
        animals.value = response.data.data || [];
    } catch (err) {
        console.error(err);
    }
};

// Create animal
const createAnimal = async () => {
    createSuccess.value = '';
    createError.value = '';

    try {
        const response = await axios.post('/api/v1/animals', createForm.value);
        createSuccess.value = response.data.message || 'Animal created successfully';

        // Clear form
        createForm.value = { name: '', species: '', preferred_environment: '', enclosure_id: null };

        fetchAnimals();
    } catch (err) {
        createError.value =
            err.response?.data?.message || 'Failed to create animal';
    }
};

// Transfer animal
const transferAnimal = async () => {
    transferSuccess.value = '';
    transferError.value = '';

    try {
        const { animal_id, target_enclosure_id } = transferForm.value;
        const response = await axios.post(`/api/v1/animals/${animal_id}/transfer`, {
            target_enclosure_id,
        });
        transferSuccess.value = response.data.message || 'Animal transferred successfully';

        // Clear form
        transferForm.value = { animal_id: null, target_enclosure_id: null };

        fetchAnimals();
    } catch (err) {
        transferError.value =
            err.response?.data?.message || 'Transfer failed';
    }
};

// Initial fetch
onMounted(() => {
    fetchAnimals();
});
</script>

<style scoped>
.tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}
.tabs button {
    padding: 8px 12px;
    cursor: pointer;
    border: 1px solid #ccc;
    background-color: #eee;
}
.tabs button.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
.tab-content {
    margin-bottom: 20px;
}
.alert {
    padding: 10px;
    margin-bottom: 10px;
}
.alert-success {
    background-color: #d4edda;
    color: #155724;
}
.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}
form div {
    margin-bottom: 10px;
}
form label {
    display: block;
    margin-bottom: 4px;
}
form input {
    padding: 6px;
    width: 100%;
    box-sizing: border-box;
}
button {
    padding: 8px 12px;
    cursor: pointer;
}
</style>

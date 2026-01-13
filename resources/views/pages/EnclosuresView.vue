<template>
    <div class="enclosures-view">
        <h1>Enclosure Management</h1>

        <!-- Success / Error Messages -->
        <div v-if="success" class="alert alert-success">{{ success }}</div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <!-- Create Enclosure Form -->
        <form @submit.prevent="createEnclosure">
            <div>
                <label>Name:</label>
                <input v-model="form.name" type="text" required />
            </div>

            <div>
                <label>Type:</label>
                <input v-model="form.type" type="text" required />
            </div>

            <div>
                <label>Capacity:</label>
                <input v-model.number="form.capacity" type="number" min="1" required />
            </div>

            <button type="submit">Create Enclosure</button>
        </form>

        <!-- Enclosures List -->
        <h3>All Enclosures</h3>
        <ul>
            <li v-for="enclosure in enclosures" :key="enclosure.id">
                {{ enclosure.id }} — {{ enclosure.name }} ({{ enclosure.type }}) — Capacity: {{ enclosure.capacity }}
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Form and state
const form = ref({
    name: '',
    type: '',
    capacity: null,
});
const success = ref('');
const error = ref('');

// Enclosures list
const enclosures = ref([]);

// Fetch all enclosures
const fetchEnclosures = async () => {
    try {
        const response = await axios.get('/api/v1/enclosures');
        enclosures.value = response.data.data || [];
    } catch (err) {
        console.error(err);
    }
};

// Create enclosure
const createEnclosure = async () => {
    success.value = '';
    error.value = '';

    try {
        const response = await axios.post('/api/v1/enclosures', form.value);
        success.value = response.data.message || 'Enclosure created successfully';

        // Reset form
        form.value = { name: '', type: '', capacity: null };

        // Refresh list
        fetchEnclosures();
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to create enclosure';
    }
};

// Fetch initial list
onMounted(() => {
    fetchEnclosures();
});
</script>

<style scoped>
.enclosures-view {
    max-width: 500px;
    margin: 0 auto;
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
ul {
    margin-top: 10px;
    padding-left: 20px;
}
</style>

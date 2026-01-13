import { createRouter, createWebHistory } from 'vue-router';
import AnimalsView from "../../views/pages/AnimalsView.vue";
import EnclosuresView from "../../views/pages/EnclosuresView.vue";


const routes = [
    {
        path: '/enclosures',
        name: 'Enclosures',
        component: EnclosuresView
    },
    {
        path: '/animals',
        name: 'Animals',
        component: AnimalsView,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

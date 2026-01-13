<template>
    <div>
        <h1>Galactic Menagerie</h1>
        <nav>
            <router-link to="/enclosures">Enclosures</router-link>
            |
            <router-link to="/animals">Animals</router-link>
        </nav>
        <router-view />
    </div>
</template>

<script>
export default {};
</script>

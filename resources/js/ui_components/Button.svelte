<script lang="ts">
    export let label = "button";
    export let theme = "button";

    import { createEventDispatcher } from 'svelte';

	const dispatch = createEventDispatcher();
	const go = () => dispatch('message');
</script>

<button on:click={go} class={theme}>{label}</button>

<style>
    button {
        padding: 10px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-weight: 700;
        font-size: 20px;
    }
    .button {
        background-color: rgb(0, 255, 179);
        color: rgb(53, 53, 247);
    }
    .cancel-button {
        background-color: red;
        color: white;
    }
    .confirm-button {
        background-color: green;
        color: white;
    }
</style>
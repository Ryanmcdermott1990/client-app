<!-- Svelte pages are similar to React components or Vue single page components, the pattern is always script, style. 
The script tags enable the user to insert JS, these pages are then compiled to vanilla JS prior to runtime making performance 
much snappier  -->
<script>
    import { onMount } from "svelte";
    import { page } from "@inertiajs/inertia-svelte";
    onMount(() => {
        console.log("the component has mounted");
    });

    // An associative array is declared containing all the links, this could be added in to another
    // component that can be re-used across all of the pages on your SPA
    let links = [
        { text: "Laravel", url: "https://laravel.com/" },
        { text: "Inertia", url: "https://inertiajs.com/" },
        { text: "Svelte", url: "https://svelte.dev/" },
    ];
</script>

<!--The svelte:head tag allows you to inser elements inside the <head> of your document-->
<svelte:head>
    <title>Laravel and Svelte with Inertia</title>
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,600"
        rel="stylesheet"
    />
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: "Nunito", sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }
    </style>
</svelte:head>

<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title m-b-md">Client App</div>
        <div class="links">
            Welcome:
            <strong>
                <!--The page object is passed from Laravel to Inertia and can be used to access serverside props
                in this case the logged in user's name-->
                {$page.props.auth.user.name}
            </strong>
        </div>
        <div class="links">
            <!--Iterating through each of the links and rendering the url and text properties of the links-->
            {#each links as link}
                <a href={link.url}>{link.text}</a>
            {/each}
        </div>
        <!--The logout route link-->
        <button><a href="/logout">Logout</a></button>
    </div>
</div>

<style>
    .full-height {
        height: 100vh;
    }

    .flex-center {
        align-items: center;
        display: flex;
        justify-content: center;
    }

    .position-ref {
        position: relative;
    }

    .content {
        text-align: center;
    }

    .title {
        font-size: 84px;
    }

    .links > a {
        color: #636b6f;
        padding: 0 25px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.1rem;
        text-decoration: none;
        text-transform: uppercase;
    }

    .m-b-md {
        margin-bottom: 30px;
    }
</style>

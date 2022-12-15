<script>
    import { onMount } from "svelte";
    import Button from "../ui_components/Button.svelte";
    import { navigate } from "svelte-routing";
    
    //Lifecycle methods such as onMount are availble to utilise for each time the page renders 
    onMount(() => {
        console.log("the component has mounted");
    });

</script>

<svelte:head>
    <title>Login Client App</title>
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
        <div class="title m-b-md">
            <p>Login</p>
        </div>
        <!--The redirect request to the auith-server is triggered from this button-->
        <button><a href="/redirect"> Login with Auth2.0 Server </a></button>
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

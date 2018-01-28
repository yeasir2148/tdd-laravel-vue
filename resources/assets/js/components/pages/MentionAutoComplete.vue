<template>
    <ul v-show="showMe" >
        <li>HI</li>
    </ul>
</template>


<script>
    export default{

        props: [ 'show' ],
        data() {
            return {
                name_suggestions: ['ami','tumi','se'],
            }
        },

        computed: {
                showMe: function(){
                return this.show
            },
        },
    }
</script>
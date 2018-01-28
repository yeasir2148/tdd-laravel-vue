<template>
    <span :class="alert_type" class="alert flash-alert" v-show="show">
        {{ body }}
    </span>
</template>

<script>
    export default {
        props: ['message'],
        data() {
            return {
                type: 'success',
                body: this.message,
                show: false,
            }
        },

        created() {
            if(this.body)
            {
                this.flash();
                this.hide();
            }
        },

        computed: {
            alert_type: function(){
                return {
                    'alert-danger': this.type == 'danger',
                    'alert-success': this.type == 'success',
                }
                
            },    
        
        },

        mounted() {


            let vm = this;
            window.vueMessageBus.$on('flash',function(data){
                
                
                vm.body = data.message;
                vm.type = data.alert_type;
                vm.flash(); 
                vm.hide();
            });        
        },

        methods: {
            flash: function(){
                this.show = true;
                this.hide();
            },

            hide: function()
            {
                var vm = this;
                setTimeout(function() {
                    vm.show = false;
                },3000);
            },
        },

        events: {
            flash: function(newMessage) {
                this.body = newMessage;
                this.flash();
            },
        },


    }
</script>

<style>
    .flash-alert {
        position: fixed;
        right: 40px;
        bottom: 40px;
    }
</style>
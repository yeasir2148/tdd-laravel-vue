<template>
    <div v-if="signedIn">
        <button class="btn-sm" :class="myclass" @click="subscribeToThread" v-text="buttonText"></button>
    </div>
</template>

<script>
    export default{
        props: ['subscribed','thread'],
        data() {
            return {
                isUserSubscribed: this.subscribed,
                signedIn: window.myApp.signedIn,
            }
        },

        computed: {
            myclass: function(){
                return { 
                    'btn btn-success' : !this.isUserSubscribed,
                    'btn btn-danger' : this.isUserSubscribed,
                };
            },

            buttonText: function(){ 
                return this.isUserSubscribed ? 'Unsubscribe' : 'Subscribe';
            },
        },

        methods: {
            subscribeToThread: function(event){
                let vm = this;
                let type = this.isUserSubscribed ? 'delete' : 'post';
                if(type === 'delete')
                {
                    axios[type](route('thread.unsubscribe',{ 'channel': this.thread.channel.slug,'thread': this.thread.id }))
                        .then(function(serverResponse){
                            console.log(serverResponse);
                            vm.isUserSubscribed = !vm.isUserSubscribed;
                            flash('You are now unsubscribed from this thread!');
                        });
                }
                else 
                {
                    axios[type](route('thread.subscribe',{ 'channel': this.thread.channel.slug,'thread': this.thread.id }))
                        .then(function(serverResponse){
                            console.log(serverResponse);
                            vm.isUserSubscribed = !vm.isUserSubscribed;
                            flash('You are now subscribed to this thread!');
                        });   
                }

            },
        },
    }
</script>
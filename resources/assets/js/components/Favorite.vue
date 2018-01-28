<template>
    <button class="btn btn-md " :class="custom_classes" @click="toggleFavoriteForCurrentUser">
        <span class="glyphicon glyphicon-heart glyphicon-danger" >{{ favorite_count }}</span>
    </button>
</template>

<script>
    export default {
        props: ['reply','auth_user_id'],

        data() {
            return {
                favorite_count: this.reply.favorites_count,
                isFavoritedByCurrentUser: this.reply.isFavoritedByCurrentUser,
                reply_object: this.reply,
                auth_user: this.auth_user_id,
            }
        },

        methods: {
            toggleFavoriteForCurrentUser: function(){
                if(this.isFavoritedByCurrentUser)
                    this.unFavoriteReply();
                else
                    this.favoriteReply();
            },

            unFavoriteReply: function(){
                /////////// add functionality to delete reply ////////////
                let vm = this;
                axios.delete(route('favorite.delete',{ 'favorite' : vm.favorite_id_by_auth_user }))
                .then(function(serverResponse){
                    vm.favorite_count = serverResponse.data.favorites_count;
                    vm.isFavoritedByCurrentUser = serverResponse.data.isFavoritedByCurrentUser;
                    vm.reply_object = serverResponse.data;
                });

            },

            favoriteReply: function(){
                let vm = this;
                axios.post(route('reply.favorite',{ 'reply': this.reply.id } ))
                .then(function(serverResponse){
                    console.log(serverResponse.data);
                    vm.favorite_count = serverResponse.data.favorites_count;
                    vm.isFavoritedByCurrentUser = serverResponse.data.isFavoritedByCurrentUser;
                    vm.reply_object = serverResponse.data;
                });
            },

        },

        computed: {
            custom_classes: function(){
                this.reply_object;
                return {
                    'btn-primary': this.isFavoritedByCurrentUser,
                    'disabled': this.auth_user_id === 'null' ? true : false,
                }
            },

            favorite_id_by_auth_user: function(){           //id of the favorite model that was created by currently authenticated user
                console.log("hi: " + this.reply_object.favorites);
                let vm = this;
                let temp = '';
                let arr = vm.reply_object.favorites;        //All the favorites that this reply received from users
                for(let i=0; i < arr.length; i++)
                {
                    if(arr[i].user_id == vm.auth_user_id)   //browse all favorites & find the one that has user_id set to current user
                    {
                        temp = arr[i].id;
                        break;
                    }
                }

                return temp;                                //return the favorite id created by current user for this reply
            },


        },
    }
</script>
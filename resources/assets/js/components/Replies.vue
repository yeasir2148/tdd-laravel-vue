<template>
    <div>   
        <div v-for="(reply, index) in replies_with_pagination.data">
            <reply 
                :attributes="reply" 
                :key="reply.id" 
                :current_user_id="auth_user_id" 
                v-on:brchanged="handlemyevent"                  
                @iamdeleted="updateCollection(index)">
            </reply>
        </div>
        
        <pagination :current_paginated_dataSet="replies_with_pagination" @paginationlinkclicked="reloadNew" v-show="replies_with_pagination.last_page != 1"></pagination>
    </div>


</template>

<script>
    import Reply from './Reply.vue';
    import Pagination from './Pagination.vue';

    export default{

        components: { Reply, Pagination },

        props: ['all_replies','auth_user_id','thread_id'],

        data() {
            return {
                all_reply_items: this.all_replies,
                replies_with_pagination: false,
            }
        },

        methods: {
            updateCollection: function(index){
                console.log('inside update collection :' + index);
                this.all_reply_items.splice(index,1);                   //being used for showing total number of comments
                
                let page = '';

        //*****if there is only 1 item in current page, then upon deleting that item fetch previous page, else fetch current page***

                if(this.replies_with_pagination.from - this.replies_with_pagination.to === 0)    
                    page = this.replies_with_pagination.current_page - 1;
                else
                    page = this.replies_with_pagination.current_page;

                this.fetch(route('replies.paginated',{ 'thread' : this.thread_id, 'page' : page }));

        //*****************************************************************************************************
        
        //******************** notify thread page so that side bar count of replies can be updated *******************
                this.$emit('repliescountchanged',this.all_reply_items.length);              
            },

            fetch: function(url = null){
                let vm = this;
                console.log("url: " + url );
                if(!url){
                    axios.get(route('replies.paginated',{ 'thread' : this.thread_id }))
                    .then(function(serverResponse){
                        vm.replies_with_pagination = serverResponse.data;           //'replies_with_pagination' holds the paginated-
                    });                                                             //-collection returned by laravel for the current
                }                                                                 //-paginated page
                else
                {
                    
                    var page = url.match(/\?page=(\d+)/i)[1];

                    console.log(page);
                    axios.get(route('replies.paginated',{ 'thread' : this.thread_id, 'page' : page }))
                    .then(function(serverResponse){
                        vm.replies_with_pagination = serverResponse.data; 
                    });     
                }                                                                

            },

            handlemyevent: function(newBestReplyId){
                console.log('new best reply: ' + newBestReplyId);
            },

            reloadNew: function(url){
                this.fetch(url);
                window.scrollTo(0,0);
            },
        },

        created() {
            console.log('replies component created');
            this.fetch();

            window.vueMessageBus.$on('notifyingrepliescomponent',newReply => {
               console.log('added object: ' + newReply);
               this.all_reply_items.push(newReply);
               
               //this.fetch(route('replies.paginated',{ 'thread' : this.thread_id, 'page' : this.pageForQueryString }));
               this.reloadNew(route('replies.paginated',{ 'thread' : this.thread_id, 'page' : this.pageForQueryString }));
               
               //this.reloadNew(this.pageForQueryString);
               
               this.$emit('repliescountchanged',this.all_reply_items.length);
            });        
        },

        computed: {
            /*showPagination: function()
            {
                return this.replies_with_pagination.last_page != 1;
            },*/

            pageForQueryString: function(){
                if(this.replies_with_pagination.total % this.replies_with_pagination.per_page === 0)
                    return this.replies_with_pagination.last_page + 1;
                else
                    return this.replies_with_pagination.last_page;
            }
        },



        
    }
</script>
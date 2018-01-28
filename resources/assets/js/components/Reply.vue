<template>
    <div class="panel" :id="replyId" :class="classconditional">
        <div class="panel-heading">
            <div class="flex-container">
                <h5 class="stretch">
                    <span v-text="replyCreatedOn"></span>, 
                    <a :href="userProfileRoute" v-text="attributes.owner.name"></a> said<b>...</b>:
                </h5>

                <div v-show="signedIn">
                    <favorite :reply="attributes" :auth_user_id="current_user_id">
                        
                    </favorite>
                </div>
                    
            </div>
        </div>
        <div class="panel-body">
            <div v-if="edit">
                <div class="form-group">
                    <textarea class="form-control" name="edit-reply" rows="2" v-model="reply_body"></textarea>

                </div>
                <!--button type="button" class="btn btn-xs btn-link" @click="updateReply">Update via ajax</button-->
                <button type="button" class="btn btn-xs btn-link" @click="edit = false">Cancel</button>
                <button type="button" class="btn btn-xs btn-link" @click="updateWithAxios">with axios</button>

            </div>
            <div v-else v-html="reply_body">
                
            </div>
        </div>
        
        <div class="panel-footer flex-container">
            <template v-if="canupdate">
                <div class="stretch">
                    <button type="button" class="btn btn-info btn-xs mr-1" @click.prevent="prepareEdit">Edit</button>
                    <button type="button" class="btn btn-danger btn-xs" @click="deleteReply">Delete</button>
                </div>
            </template>
            <div style="margin-left:auto" v-if="canUpdateThread && !this.is_best_reply">
                <button type="button" class="btn btn-success btn-xs" @click="markBestReply">Best reply?</button>
            </div> 
        </div>

    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    export default{
        props: ['attributes','current_user_id','thread_id'],

        components: { Favorite },

        data() {
            return{

                edit: false,
                reply_body : this.attributes.body,
                cons_body : this.attributes.body,
                //route: 'tddforum.com/reply/update/'+this.attributes.id,
                canupdate: this.attributes.can_update,
                reply_before_editing: false,
                is_best_reply: this.attributes.is_best_reply,
             }
        },

        computed: {
            signedIn : function() {
                return window.myApp.signedIn;
             },

             userProfileRoute: function(){
                return window.route('user.profile',{ 'user' : this.attributes.owner.id });
             },

             replyCreatedOn: function(){
                return moment(this.attributes.created_at).fromNow();
             },

             replyId: function(){
                return "Reply_" + this.attributes.id;
             },
             canUpdateThread: function(){
                if(!this.current_user_id)
                    return false;
                else
                    return this.attributes.thread_owner == window.myApp.user.id;
             },
             classconditional: function() {
                return {
                    'panel-success' : this.is_best_reply,
                    'panel-default' : !this.is_best_reply,
                }
             },

        },

        created() {
            window.vueMessageBus.$on('brchanged',newBrId => {
                if(this.attributes.id != newBrId)
                {
                    this.is_best_reply = false;
                }
            });
        },

        methods: {

            prepareEdit: function(){
                this.reply_before_editing = this.reply_body;
                this.edit = true;
            },

            updateWithAxios: function(event)
            {
                console.log('fired');
                let vm = this;

                axios.post(route('reply.update',{ 'reply' : this.attributes.id }), {
                    reply_body: this.reply_body,                    
                }).then(function(serverResponse){
                    vm.reply_body = serverResponse.data.body;
                    flash('reply has been updated','success');
                }).catch(errorResponse => {
                    vm.reply_body = vm.reply_before_editing;        //reset the reply body to what it was before editing
                    console.log(errorResponse.response.data.errorMessage);
                    window.flash(errorResponse.response.data.errorMessage,'danger');
                });

                this.edit = false;
                
            },

            updateReply: function(event)
            {
                console.log(event.target.tagName);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var comp = this;

                $.ajax({
                    dataType: 'json',
                    type: 'post',
                    data: { reply_body: this.reply_body },
                    url: route('reply.update',this.attributes.id),
                    success: function(ajaxResp)
                        {
                            console.log("ajax returned: " + ajaxResp);
                            comp.edit = false;
                            this.reply_body = ajaxResp.body;

                        },
                    error: function(ajaxResp){
                            console.log('error: ' + ajaxResp);
                        }
                });
                
            },

            deleteReply: function(){
                let vm = this;
                axios.delete(route('reply.delete',{'reply' : this.attributes.id}))
                .then(function(serverResponse){
                    
                    window.flash('reply removed');   
                    vm.$emit('iamdeleted', vm.attributes.id);
                    //window.flash('reply removed');   
                    
                }).catch(function(error){
                    console.log("error: "+ error.message);
                });
            },

            markBestReply: function(){
                let vm = this;
                axios.post(route('thread.best-reply.store',{ 'thread': this.attributes.thread_id, 'reply': this.attributes.id })).then(function(serverResponse){
                        console.log(serverResponse.data);
                        vm.is_best_reply = true;
                        //vm.$emit('brchanged',vm.attributes.id);
                        window.vueMessageBus.$emit('brchanged',vm.attributes.id);
                    });

            },
        },
        
    }
</script>
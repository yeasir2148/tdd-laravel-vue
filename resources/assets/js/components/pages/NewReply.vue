<template>
    <div v-if="signedIn">
        <div class="panel panel-success">
            <div class="panel-body">
                
                <div class="form-group">
                    <textarea class="form-control" name="body" id="new_reply" rows="3" style="width:100%" placeholder="Have something to say?" v-model="reply_body" ></textarea>
                </div>
                <button type="submit" class="btn btn-success btn-sm pull-right" @click="postReply">Post Reply</button>
                
            </div>
        </div>

        <!--mentionautocomplete :show="showMentionComp" :style="mentionStyle" id="xyz"></mentionautocomplete-->
    </div>

    <div v-else>
        <p class="text-center">
            Please <a :href="loginRoute">login</a> to leave a reply
        </p>
    </div>



</template>

<script>
    import 'jquery.caret';
    import Mentionautocomplete from './MentionAutoComplete';

    export default {

        components: { Mentionautocomplete },

        props: ['thread','is_curr_user_subscribed'],

        data() {
            return {
                reply_body: '',
                post_reply_url: route('reply.store', { 'thread': this.thread } ),
                curr_user_subscribed: this.is_curr_user_subscribed,
                showMentionComp: false,
                mentionStyle : {
                    position: 'relative',
                    left: '0px',
                    top: '0px',
                    'list-style-type': 'none',
                    
                },
                //textarea_left: null,
                //container_top: null,
                textarea_left: null,
                textarea_top: null,                
            }
        },

        mounted() {


            
        },

        computed: {
            signedIn: function()
            {
                return window.myApp.signedIn;
            },

            loginRoute: function(){
                return window.route('login');
            },
        },

        methods: {
            postReply: function(){
                
                axios.post(this.post_reply_url, { 'body': this.reply_body, })
                .then(serverResponse => {
                    flash('new reply posted','success');
                    console.log(serverResponse.data);
                    this.reply_body = '';
                   
                    // notifying parent that a new reply was added
                    this.$emit('newreplyadded', serverResponse.data);

                    //if current user is subscribed to this thread then emit an event so that nav-notification component can reload 
                    //notifications for that user

                    if(this.curr_user_subscribed)
                    {
                        window.vueMessageBus.$emit('eventFetchNotifications');
                    }

                    //console.log(moment().format('YYYY-MM-DD'));
                }).catch(function(errorObject){
                    if(errorObject.response.status === 429)         //status code 429 mean cannot reply again within 1 minute 
                    {
                        console.log(errorObject.response.data);
                        window.flash(errorObject.response.data,'danger');
                    }
                    else
                    {
                        console.log(errorObject.response.data.errorMessage);
                        window.flash(errorObject.response.data.errorMessage,'danger');                        
                    }                    
                });
            },

            startMentionFunc: function(event){                    

            },
        },

        
    }
</script>
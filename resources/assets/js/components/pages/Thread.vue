<script>
    import Replies from '../Replies.vue';
    import NewReply from './NewReply.vue';
    import ThreadSubscribe from './ThreadSubscribe.vue';
    import Mentionautocomplete from './MentionAutoComplete';

    export default {
        components: { Replies, NewReply, ThreadSubscribe, Mentionautocomplete },

        props: ['auth_user_id','initial_replies_count'],

        data() {
            return {
                replies_count: this.initial_replies_count,
                showRepliesPanel: true,
            }
        },

        methods: {
            updaterepliescount: function(length){
                console.log('new length: ' + length);
                this.replies_count = length;
            },

            updateReplyList: function(newReply){
                console.log('caught inside thread.vue in method updateReplyList :' + newReply);
                window.vueMessageBus.$emit('notifyingrepliescomponent',newReply);
            },
        }
    }
</script>
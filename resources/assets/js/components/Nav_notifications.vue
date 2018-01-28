<template>
    <li class="btn-group dropdown">
        <!--a class="btn btn-default top-nav-dropdown-badge">Hi</a-->
        <a class="btn btn-default btn-md top-nav-dropdown"><span class="glyphicon glyphicon-bell glyphicon-blue"></span><span class="badge badge-primary" v-text="notifications.length"></span></a>
        <a class="btn btn-default btn-md top-nav-dropdown-caret dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </a>
        <ul class="dropdown-menu" v-show="notifications.length">
            <li v-for="(notification,index) in notifications">
                <a :href="notification.data.reply_link" style="font-size:10px" @click="markAsRead(notification, index)">
                    <div style="font-weight:bold" v-text="notification.data.thread_title"></div>
                    <div style="margin-bottom:3px; display:inline" v-text="notification.data.reply_creator"></div><span> replied...</span>
                </a>
            </li>
        </ul>
        <ul class="dropdown-menu" v-show="!notifications.length">
            <li><a>No notifications</a></li>
        </ul>
    </li>
</template>

<script>
    export default{
        props:['passed_notifications','curr_user'],
        data() {
            return {
                notifications: this.passed_notifications,
                auth_user_id: this.curr_user,
            }
        },

        //************* no use **************************
        created() {
            window.vueMessageBus.$on('eventFetchNotifications', () => {
                this.fetchNotifications();
            });
        },

        //***********************************************

        mounted(){
            //setInterval(this.fetchNotifications,10000);
        },

        methods: {
            fetchNotifications: function(){
                let vm = this;
                axios.get(route('notifications.unread',{ 'user':this.auth_user_id }))
                    .then(function(serverResponse){
                        console.log(serverResponse.data);
                        vm.notifications = serverResponse.data;
                    });
            },

            markAsRead: function(notification, indexOfNotification)
            {
                let vm = this;
                axios.delete(route('notification.delete',{ 'user':this.auth_user_id, 'notification':notification.id }))
                    .then(serverResponse => {
                        this.notifications.splice(indexOfNotification,1);
                    });
            }
        }
    }
</script>
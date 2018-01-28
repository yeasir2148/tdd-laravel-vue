let authorizations = {
    canUpdateReply : 
        function(reply)
        {
            if(!window.myApp.user)
                return false;

            return window.myApp.user.id == reply.owner.id;
        },
};

module.exports = authorizations;
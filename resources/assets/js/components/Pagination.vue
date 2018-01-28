<template>
    <div>
        <nav aria-label="...">
          <ul class="pagination">
            <li class="page-item" v-if="prev_url">
              <a id="rep_paginator_prev" class="page-link" :href="prev_url" @click.prevent="fetchPrev">Previous</a>
            </li>
            <li class="page-item" v-if="show_first_link">
                <a class="page-link" :href="prev_url" @click.prevent="fetchPrev">{{ first_link_text }}</a>
            </li>
            <li class="page-item active" v-if="show_current_link">
              <a class="page-link" :href="current_url" @click.prevent="fetchCurr">{{ current_link_text }}<span class="sr-only">(current)</span></a>
            </li>
            <li class="page-item" v-if="show_next_link">
                <a class="page-link" :href="next_url" @click.prevent="fetchNext">{{ next_link_text }}</a>
            </li>
            <li class="page-item" v-if="next_url">
              <a id="rep_paginator_next" class="page-link" :href="next_url" @click.prevent="fetchNext">Next</a>
            </li>
          </ul>
        </nav>
    </div>
</template>

<script>
    export default{

        props: ['current_paginated_dataSet'],

        data() {
            return {

                item: this.current_paginated_dataSet,

                prev_url: this.current_paginated_dataSet.prev_page_url,
                next_url: this.current_paginated_dataSet.next_page_url,
                
                first_link_text: '1',
                current_link_text: 'curr',
                next_link_text: '3',
                
                total_pages: this.current_paginated_dataSet.total,
                
                current_url: this.current_paginated_dataSet.path + '?page=' + this.current_paginated_dataSet.current_page,
                
            }
        },

        methods: {

            fetchPrev: function(){
                this.$emit('paginationlinkclicked',this.prev_url);
            },

            fetchCurr: function(){
                this.$emit('paginationlinkclicked',this.current_url);
            },

            fetchNext: function(){
                this.$emit('paginationlinkclicked',this.next_url);
            },

        },

        computed: {
            show_first_link: function(){
                return this.prev_url;
            },

            show_current_link: function(){
                return this.total_pages;
            },

            show_next_link: function(){
                return this.next_url;
            },
        },

        mounted(){
            if(!this.prev_url){
                //console.log("here: " + $("#rep_paginator_prev").get());
                $("#rep_paginator_prev").removeAttr("href");
            }

            if(!this.next_url){
                $("#rep_paginator_next").removeAttr("href");
            }
        },

        watch: {
            current_paginated_dataSet() {
                this.item = this.current_paginated_dataSet;
                this.prev_url = this.current_paginated_dataSet.prev_page_url;
                this.next_url = this.current_paginated_dataSet.next_page_url;
                this.current_url = this.current_paginated_dataSet.path + '?page=' + this.current_paginated_dataSet.current_page;

                this.total_pages = this.current_paginated_dataSet.total;
                this.first_link_text = this.current_paginated_dataSet.current_page - 1;
                this.current_link_text = this.current_paginated_dataSet.current_page;
                this.next_link_text = this.current_paginated_dataSet.current_page + 1;
            },
        },

    }
</script>
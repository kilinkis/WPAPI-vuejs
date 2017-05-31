<?php

/*

    SOURCES:

    https://torquemag.io/2017/02/introduction-vuejs-wordpress-rest-api/
    https://torquemag.io/2017/02/using-vuejs-components-wordpress-rest-api/
    https://torquemag.io/2017/02/using-vuejs-router-wordpress-rest-api/

    https://github.com/caldera-learn/vue-js-example-theme

*/

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
                <div id="app">
                    <p>
                        <router-link to="/">Home</router-link>
                        <router-link to="/posts">Posts</router-link>
                    </p>
                    <router-view></router-view>
                </div>

                <div id="home"></div>

                <script type="text/html" id="post-list-tmpl">
                    <div id="posts">
                        <div v-for="post in posts">
                            <article v-bind:id="'post-' + post.id">
                                <header>
                                    <h2 class="post-title">
                                        {{post.title.rendered}}
                                    </h2>
                                </header>
                                <!-- <div class="entry-content" v-html="post.excerpt.rendered"></div> -->

                                <div class="entry-content" v-html="post.excerpt.rendered" v-if="list"></div>
                                <div class="entry-content" v-html="post.content.rendered" v-if="post.id == show"></div>
                                <a href="#" class="close" role="button" v-on:click.stop="close" v-if="post.id == show">Close</a> <!-- the click event's propagation will be stopped, por eso tiene el .stop https://vuejs.org/v2/guide/events.html-->
                                <a href="#" class="read-more" role="button" v-on:click.stop="read(post.id)" v-if="list">Read More now</a>

                                <router-link :to="{ name: 'post', params: { id: post.id }}">
                                    Single template
                                </router-link>
                            </article>
                        </div>
                    </div>
                </script>

                <script type="text/html" id="post-tmpl">
                    <div class="post">

                        <article v-bind:id="'post-' + post.id">
                            <header>
                                <h2 class="post-title">
                                    {{post.title}} by {{post.author}}
                                </h2>
                            </header>
                            <div class="entry-content" v-html="post.content"></div>

                        </article>
                    </div>
                </script>

                <script>
                (function($){
                    var config = {
                        api: {
                            posts: "<?php echo esc_url_raw( rest_url( 'wp/v2/posts/' ) ); ?>"
                        },
                        nonce: "<?php echo wp_create_nonce( 'wp_rest' ); ?>"
                    };

                //$.when( $.get( config.api.posts ) ).then( function( d ){
                    var posts = Vue.component('post-list', {
                        template: '#post-list-tmpl',
                        data: function() {
                            return{
                                list: true,
                                show: 0,
                                posts: []
                            }
                        },
                        mounted: function () {
                            this.getPosts();
                        },
                        methods: {
                            getPosts: function () {
                                var self = this;
                                $.get( config.api.posts, function( r ){
                                    self.$set( self, 'posts', r );
                                });

                            },
                            read: function( id ){
                                this.list = false;
                                this.show = id;
             
                            },
                            close: function(){
                                this.list = true;
                                this.show = 0;
                            }
                        }
                    });

                    /* es muy probable que esto no haga falta since el new se hace mas abajo, con el router
                    vue = new Vue({
                        el: '#app',
                        data: {}
                    });*/

                //});

                    var post = Vue.component( 'post', {
                        template: '#post-tmpl',
                        data: function() {
                            return{
                                post: []
                            }
                        },
                        mounted: function () {
                            this.getPost();
                        },
                        methods: {
                            getPost: function(){
                               var self = this;
                               $.get( config.api.posts +  self.$route.params.id, function(r){ // this.$route.params.id gets the ID from the url 
                                   r.title = r.title.rendered;
                                   r.content = r.content.rendered;
                                   r.author = r.author; // con este id tengo que pedir http://wocker.dev/wp-json/wp/v2/users/1"
                                   self.$set( self, 'post', r );
                                });
                            }
                        }
                    });

                    //var name = "adsasd";

                    var home = Vue.component('home',{
                        template: '<div>Hi Juan!</div>'
                    });


                    var router = new VueRouter({
                        mode: 'history',
                        routes: [
                            { path: '/', name: 'home', component: home },
                            { path: '/posts', name: 'posts', component: posts },
                            { path: '/posts/:id', name: 'post', component: post },
                        ]
                    });


                    new Vue({
                        router,
                    }).$mount('#app')



                })( jQuery );

            </script>


		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>

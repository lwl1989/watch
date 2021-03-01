<template>
    
</template>

<script>
    export default {
        name: "loadingComponent",
        created:function () {
            let that = this;
            axios.get('/api/admin/routes').then(function (response) {
                if (response.data.code == 0) {
                    let rou = response.data.response.router;

                    rou.forEach((item, index) => {
                        if ("children" in item && item.children.length > 0) {
                            item.children.forEach((son, key) => {
                                item.children[key].component = replaceComponent(son.component)
                            });
                        }
                        item.component = replaceComponent(item.component);
                        console.log(that.routes);
                    });
                   // this.routes.push('/');
                } else {
                    // window.location.href = '/';
                }
            }).catch(function (error) {
                // window.location.href = '/';
            });
            console.log(this)
        }
    }

</script>

<style scoped>

</style>
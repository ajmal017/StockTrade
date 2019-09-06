<script>
    import VueCharts from 'vue-chartjs'
    import { Line } from 'vue-chartjs'
    export default Line.extend({
        props: ["data", "options"],
        mounted () {
            this.renderChart(this.data, this.options)
        }
    })
</script>

<style lang="css">
</style>

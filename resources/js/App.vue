<template>
  <div class="px-6 py-3">
    <Table @remove="remove" :reports="reports"></Table>
    <Loader v-if="isLoading"></Loader>
  </div>
</template>

<script>
import Table from "./components/Table.vue";
import Loader from "./components/Loader.vue";
export default {
  setup() {
    return {
      url: "http://localhost/api/users",
    };
  },
  data() {
    return {
      reports: [],
    };
  },
  components: {
    Table,
    Loader,
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  methods: {
    remove($e) {
      console.log("$e ==>> ", $id);
      const index = this.reports.findIndex((r) => r.id == $id);
      if (index >= 0) {
        this.reports = this.reports.splice(index, 1);
      }
    },
  },
  mounted() {
    this.$store.dispatch("getAllreports");
  },
};
</script>

<style lang="scss" scoped></style>

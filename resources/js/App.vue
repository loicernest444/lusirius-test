<template>
  <div class="px-6 py-3">
    <Table @remove="remove" :reports="reports"></Table>
    <div class="text-red-500">
      How To Install Vue 3 in Laravel 9 with Vite - TechvBlogs
    </div>
  </div>
</template>

<script>
import Table from "../components/Table.vue";
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

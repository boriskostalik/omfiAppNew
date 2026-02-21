<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
const props = defineProps({
  year: { type: [String, Number], required: true },
  issue: { type: Object, required: true }, 
  imageUrl: { type: String, default: "/images/hero-omfi.jpg" },
});
const bottomLine = computed(() => {
  const parts = [];
  if (props.issue?.volume) parts.push(`Volume ${props.issue.volume}`);
  if (props.issue?.number) parts.push(`Číslo ${props.issue.number}`);
  if (props.year) parts.push(`Rok ${props.year}`);
  return parts.join(" • ");
});

const go=(id)=>router.get(route('archive.issue',id))

</script>
<template>
  <button
    type="button"
    @click="go"
    class="group w-full text-left"
    :disabled="!issue.hasPublication"
  >
    <div
      class="relative aspect-[3/4] overflow-hidden rounded-2xl shadow-lg transition
             group-hover:-translate-y-0.5 group-hover:shadow-xl"
      :class="!issue.hasPublication ? 'opacity-30 cursor-not-allowed group-hover:translate-y-0' : ''"
    >
      <div
        class="absolute inset-0 bg-cover bg-center transition-transform duration-300 group-hover:scale-[1.04]"
        :style="{ backgroundImage: `url('${imageUrl}')` }"
      ></div>
      <div class="absolute left-6 right-6 top-6">
        <div class="[font-family:'Poppins',sans-serif] text-[13px] font-semibold tracking-wide text-black drop-shadow">
          {{ props.year }}/{{ props.issue.number }}
        </div>
        <div class="mt-2 [font-family:'Poppins',sans-serif] text-[30px] leading-[1.05] font-semibold text-black drop-shadow">
          OMFI
        </div>
      </div>
      <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/40 to-transparent"></div>
      <div class="absolute left-6 right-6 bottom-6">
        <div class="[font-family:'Poppins',sans-serif] text-[14px] font-semibold text-white drop-shadow">
          {{ bottomLine }}
        </div>
        <div v-if="!issue.hasPublication" class="mt-1 text-xs text-white/80">
          Bez publikácií
        </div>
      </div>
    </div>
  </button>
</template>

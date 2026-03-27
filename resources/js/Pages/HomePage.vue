<script setup>
import { Link } from "@inertiajs/vue3";
import IssueCoverCard from "@/Components/IssueCoverCard.vue";

const props = defineProps({
    latest: Object,
    stats: Object,
});
</script>

<template>
    <div
        class="relative h-[260px] w-full overflow-hidden bg-center bg-cover"
        style="background-image: url(&quot;/images/hero.png&quot;)"
    >
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(10,30,60,0.85) 0%, rgba(10,30,60,0.35) 60%, transparent 100%)"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-full max-w-4xl mx-auto px-6 text-center">
                <h1 class="text-4xl sm:text-5xl font-bold text-white tracking-tight leading-tight">
                    Obzory matematiky, fyziky a informatiky
                </h1>
                <div class="mt-2 flex items-center justify-center gap-3">
                    <div class="h-0.5 w-12 bg-white/60"></div>
                    <p class="text-white font-medium tracking-widest uppercase text-xl">
                        Elektronický archív
                    </p>
                    <div class="h-0.5 w-12 bg-white/60"></div>
                </div>
            </div>
        </div>
    </div>
    <section class="mt-14">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                <aside
                    class="lg:col-span-4 order-1 lg:order-2 mx-auto max-w-sm text-center lg:text-left lg:mx-0 lg:max-w-none"
                >
                    <div
                        class="flex items-end justify-center lg:justify-between gap-4"
                    >
                        <div>
                            <div
                                class="text-2xl font-semibold tracking-widest uppercase text-slate-900"
                            >
                                Aktuálne vydanie
                            </div>
                        </div>
                    </div>
                    <div class="mt-7">
                        <IssueCoverCard
                            v-if="props.latest"
                            :year="props.latest.year"
                            :issue="{
                                id: props.latest.id,
                                number: props.latest.number,
                                volume: props.latest.volume,
                                hasPublication: true,
                            }"
                            image-url="/images/hero-omfi2.jpg"
                        />
                    </div>
                </aside>
                <div class="lg:col-span-8 order-2 lg:order-1">
                    <h2 class="text-3xl font-bold text-black">
                        Poslanie časopisu
                    </h2>
                    <p class="mt-5 text-[18px] leading-8 text-black">
                        Časopis je určený prednostne pre učiteľov matematiky,
                        fyziky a informatiky na základných, stredných a vysokých
                        školách. Publikuje metodické materiály, skúsenosti
                        učiteľov, nové poznatky z teórie vyučovania a nové
                        vedecké poznatky z jednotlivých vedných odborov.
                    </p>
                    <p class="mt-4 text-[18px] leading-8 text-black">
                        Redakcia vytvára priestor pre publicitu výsledkov
                        vedeckých aktivít učiteľov z jednotlivých stupňov škôl a
                        študentov 3. stupňa štúdia (doktorandov).
                    </p>
                    <h3 class="mt-10 text-[20px] font-semibold text-black">
                        Technické informácie pre autorov
                    </h3>
                    <ul
                        class="mt-4 list-disc pl-7 space-y-2 text-[18px] leading-8 text-black"
                    >
                        <li>
                            Príspevky: MS Word (2008+) alebo LaTeX/AMSTeX
                            (matematické príspevky)
                        </li>
                        <li>Vhodné typy súborov: .doc, .wmf, postscript</li>
                        <li>Font: Times New Roman CE</li>
                        <li>V odsekoch nepoužívajte odsadenie prvého riadku</li>
                        <li>
                            Obrázky z grafického editora; skeny len v nutných
                            prípadoch
                        </li>
                        <li>
                            Pridať súhrn v SK aj EN (cca 8 riadkov), kľúčové
                            slová a MESC kódy, a nadpis aj v angličtine
                        </li>
                    </ul>

                    <p class="mt-5 text-[16px] leading-7 text-slate-700">
                        Ostatné parametre štýlu (rozmery stránky, riadkovanie,
                        odrážky, číslovanie) prispôsobí redakcia.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="mt-16">
        <div
            class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden"
        >
            <div class="max-w-6xl mx-auto px-4">
                <div class="grid grid-cols-1 sm:grid-cols-3">
                    <Link
                        :href="route('search.index')"
                        class="relative py-8 text-center sm:border-r border-slate-200 block hover:bg-slate-100 transition-colors"
                    >
                        <div class="absolute left-0 right-0 top-0 h-px bg-slate-200"></div>
                        <div class="absolute left-0 right-0 bottom-0 h-px bg-slate-200"></div>
                        <div class="mx-auto mb-8 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#1E4E8C] text-white font-extrabold text-2xl shadow-sm">
                            {{ props.stats?.publications ?? "—" }}
                        </div>
                        <div class="text-sm font-bold tracking-widest uppercase text-slate-900">
                            Počet publikácií
                        </div>
                    </Link>
                    <Link
                        :href="route('search.index', { typ: 'autori' })"
                        class="relative py-8 text-center sm:border-r border-slate-200 block hover:bg-slate-100 transition-colors"
                    >
                        <div class="absolute left-0 right-0 top-0 h-px bg-slate-200"></div>
                        <div class="absolute left-0 right-0 bottom-0 h-px bg-slate-200"></div>
                        <div class="mx-auto mb-8 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#1E4E8C] text-white font-extrabold text-2xl shadow-sm">
                            {{ props.stats?.authors ?? "—" }}
                        </div>
                        <div class="text-sm font-bold tracking-widest uppercase text-slate-900">
                            Počet autorov
                        </div>
                    </Link>
                    <Link
                        :href="route('archive.index')"
                        class="relative py-8 text-center block hover:bg-slate-100 transition-colors"
                    >
                        <div class="absolute left-0 right-0 top-0 h-px bg-slate-200"></div>
                        <div class="absolute left-0 right-0 bottom-0 h-px bg-slate-200"></div>
                        <div class="mx-auto mb-8 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#1E4E8C] text-white font-extrabold text-2xl shadow-sm">
                            {{ props.stats?.issues ?? "—" }}
                        </div>
                        <div class="text-sm font-bold tracking-widest uppercase text-slate-900">
                            Počet vydaní
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

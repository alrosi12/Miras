import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

/** أيام الخطة + تمارين متداخلة (حقول name/description تبقى خارج Alpine). */
document.addEventListener('alpine:init', () => {
    Alpine.data('workoutPlanDays', (initialDays, exerciseOptions) => ({
        days:
            initialDays?.length > 0
                ? structuredClone(initialDays)
                : [
                      {
                          day_name: '',
                          order: 0,
                          exercises: [
                              {
                                  exercise_id: '',
                                  sets: 3,
                                  reps: 10,
                                  rest_seconds: 60,
                                  order: 0,
                              },
                          ],
                      },
                  ],
        exerciseOptions: exerciseOptions ?? [],
        exerciseFilter: '',
        dragSource: null,
        filteredExerciseOptions() {
            const q = (this.exerciseFilter || '').toLowerCase().trim();
            if (!q) {
                return this.exerciseOptions;
            }

            return this.exerciseOptions.filter((o) => (o.name || '').toLowerCase().includes(q));
        },
        addDay() {
            this.days.push({
                day_name: '',
                order: this.days.length,
                exercises: [
                    {
                        exercise_id: '',
                        sets: 3,
                        reps: 10,
                        rest_seconds: 60,
                        order: 0,
                    },
                ],
            });
        },
        removeDay(index) {
            if (this.days.length <= 1) {
                return;
            }
            this.days.splice(index, 1);
        },
        addExercise(dayIndex) {
            const day = this.days[dayIndex];
            day.exercises.push({
                exercise_id: '',
                sets: 3,
                reps: 10,
                rest_seconds: 60,
                order: day.exercises.length,
            });
        },
        removeExercise(dayIndex, exIndex) {
            const day = this.days[dayIndex];
            if (day.exercises.length <= 1) {
                return;
            }
            day.exercises.splice(exIndex, 1);
        },
        dragStart(dayIdx, exIdx, event) {
            this.dragSource = { dayIdx, exIdx };
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', `${dayIdx}:${exIdx}`);
        },
        dropExercise(dayIdx, toIdx) {
            const raw = this.dragSource;
            if (!raw || raw.dayIdx !== dayIdx) {
                this.dragSource = null;

                return;
            }
            const from = raw.exIdx;
            this.dragSource = null;
            if (from === toIdx) {
                return;
            }
            const arr = this.days[dayIdx].exercises;
            const [moved] = arr.splice(from, 1);
            arr.splice(toIdx, 0, moved);
            this.syncOrders();
        },
        syncOrders() {
            this.days.forEach((day, d) => {
                day.order = d;
                day.exercises.forEach((ex, e) => {
                    ex.order = e;
                });
            });
        },
    }));

    Alpine.data('sessionSetsLive', (initialRows, patchUrls, destroyUrls = {}) => ({
        rows: structuredClone(initialRows ?? []),
        message: '',
        error: '',
        csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
        async patchRow(row) {
            this.error = '';
            this.message = '';
            const url = patchUrls[row.id];
            if (!url) {
                return;
            }
            const body = {
                reps: row.reps === '' || row.reps === null ? null : Number(row.reps),
                weight: row.weight === '' || row.weight === null ? null : Number(row.weight),
                is_completed: !!row.is_completed,
            };
            const res = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify(body),
            });
            if (!res.ok) {
                const data = await res.json().catch(() => ({}));
                this.error = data.message || (await res.text()) || 'Error';

                return;
            }
            const data = await res.json().catch(() => ({}));
            this.message = data.message || 'Saved';
            setTimeout(() => {
                this.message = '';
            }, 2000);
        },
        async removeRow(row) {
            if (!confirm('Remove this set?')) {
                return;
            }
            const url = destroyUrls[row.id];
            if (!url) {
                return;
            }
            const body = new URLSearchParams({
                _token: this.csrf,
                _method: 'DELETE',
            });
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body,
            });
            if (!res.ok) {
                this.error = 'Could not remove';

                return;
            }
            this.rows = this.rows.filter((r) => r.id !== row.id);
        },
    }));
});

Alpine.start();

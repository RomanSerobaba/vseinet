'use strict';

class Queue {
    constructor() {
        this.tasks = [];
    }

    add(task) {
        if (task instanceof Array) {
            return task.map(task => this.add(task));
        }
        this.tasks.push(task);
    }

    get() {
        return new Promise((fulfill, reject) => {
            this.check(fulfill)
        });
    }

    check(cb) {
        if (this.tasks.length) {
            return cb(this.tasks.shift());
        }
        setImmediate(() => this.check(cb));
    }
}

module.exports = Queue;

// Sample
//
// const q = new Queue();
//
// q.get().then(task => {
//     console.log(task);
// });
// q.get().then(task => {
//     console.log(task);
// });
//
// setTimeout(() => {
//     q.add([
//         { id: 1, name: 'name1' },
//         { id: 2, name: 'name2' },
//         { id: 3, name: 'name3' },
//         { id: 4, name: 'name4' },
//     ]);
//     setTimeout(() => {
//         console.log(q);
//     }, 100);
// }, 1000);
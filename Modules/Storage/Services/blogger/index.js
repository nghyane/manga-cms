import { Worker, isMainThread, parentPort } from 'worker_threads';

if (isMainThread) {
    const worker = new Worker(__filename);

    worker.on('message', (message) => {


    });

} else {
    parentPort.on('message', (message) => {


    });
}

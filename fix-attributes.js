import { LspClient, JSONRPCEndpoint } from "ts-lsp-client";
import {spawn} from "child_process";
import {pathToFileURL} from "url";
import fs from 'node:fs';
import process from 'process';

async function getDeclaration(file, line, character) {
    const root = "/home/ilutov/Developer/php-src";
    const clangd = spawn("clangd", [], { shell: true, stdio: 'pipe', cwd: root });
    const endpoint = new JSONRPCEndpoint(clangd.stdin, clangd.stdout);
    const client = new LspClient(endpoint);

    await client.initialize({
        workspaceFolders: [{ name: 'workspace', uri: pathToFileURL(root).href }],
        processId: clangd.pid,
    });

    await client.didOpen({
        textDocument: {
            uri: pathToFileURL(file).href,
            languageId: "c",
            version: 1,
            text: fs.readFileSync(file).toString(),
        },
    });

    const result = await client.gotoDeclaration({
        textDocument: { uri: pathToFileURL(file).href },
        position: { line: line - 1, character: character - 1 },
    });

    await client.didClose({ uri: pathToFileURL(file).href });

    clangd.kill('SIGKILL');

    return result;
}

const lines = fs.readFileSync("suggestions.txt").toString().split("\n");
for (let i = 0; i < lines.length; i++) {
    process.stdout.write('.');

    const line = lines[i];
    // /home/ilutov/Developer/php-src/Zend/zend_vm_opcodes.c: In function ‘zend_get_opcode_name’:
    if (line.match(/(\/home.*\.c): In function/)) {
        const matches = lines[i+1].match(/(\/home\/.*\.c):(\d+):(\d+): .* \[-Wsuggest-attribute=(pure|const)\]/);
        if (!matches) continue;

        const file = matches[1];
        const line = matches[2];
        const column = matches[3];
        const type = matches[4];
        const declaration = (await getDeclaration(file, line, column))[0];
        if (!declaration) continue;

        const uri = declaration.uri.substring("file://".length);
        if (!uri.endsWith('.h')) continue;

        const modifiedLines = fs.readFileSync(uri).toString().split("\n");
        let modifiedLine = modifiedLines[declaration.range.start.line];
        modifiedLine = modifiedLine.replace("ZEND_API", "ZEND_API ZEND_" + type.toUpperCase());
        modifiedLine = modifiedLine.replace("PHPAPI", "PHPAPI ZEND_" + type.toUpperCase());
        modifiedLines[declaration.range.start.line] = modifiedLine;
        fs.writeFileSync(uri, modifiedLines.join("\n"));

        process.stdout.write('W');
    }
}

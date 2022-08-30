import React, { useEffect, useState } from 'react';
import tw from "twin.macro";
import Button from "@/components/elements/Button";
import { ServerContext } from "@/state/server";
import { SocketEvent } from "@/components/server/events";
import Fade from "@/components/elements/Fade";
import { SwitchTransition } from "react-transition-group";
import styled, { keyframes } from "styled-components/macro";
import stripAnsi from 'strip-ansi';
import shareServerLog, { PasteResponse } from '../../../api/server/shareServerLog';
import { IconProp, library } from '@fortawesome/fontawesome-svg-core';
import * as Icons from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

Object.keys(Icons)
    .filter(it => it !== 'key' && it !== 'prefix')
    // @ts-ignore
    .forEach(it => library.add(Icons[it]))

interface Props {
    position: 'component' | 'commandLine'
}

interface MCPasteData {
    tokenValid: boolean,
    style: MCPasteStyle
}

interface MCPasteStyle {
    buttonLocation: 'component' | 'commandLine'
    textButtonText: string
    textColor: string
    textButtonColor: string
    textButtonHoverColor: string
    boxColor: string
    textSize: 'text-xs' | 'text-sm' | 'text-base' | 'text-lg' | 'text-xl'
    buttonSize: 'xsmall' | 'small' | 'large' | 'xlarge'
    shadow: boolean
    icon: string
    iconColor: string
    iconHoverColor: string
    toastTextColor: string
    toastBoxColor: string
    toastBorderColor: string
    toastOpacity: number
    toastText: string
    toastErrorText: string
}

// @ts-ignore
export const mcPasteData: MCPasteData = window.MCPasteData
export const mcPasteStyle: MCPasteStyle = mcPasteData.style

const fade = keyframes`
    from { opacity: 0 }
    to { opacity: 1 }
`;

const Toast = styled.div`
    ${tw`fixed z-50 bottom-0 left-0 mb-4 w-full flex justify-end pr-4`};
    animation: ${fade} 250ms linear;

    & > div {
        ${tw`rounded px-4 py-2 border`};
        background-color: ${mcPasteStyle.toastBoxColor};
        border-color: ${mcPasteStyle.toastBorderColor};
        opacity: ${mcPasteStyle.toastOpacity / 100};
    }

    & p {
        color: ${mcPasteStyle.toastTextColor};
    }
`;

// I took the lazy approach when doing these 2 elements, it was 3:11 am when I was doing this so.....
const CMDButton = styled.div`
    ${tw`p-1 transition-all rounded-br cursor-pointer`}

    &:hover {
        background-color: ${mcPasteStyle.iconHoverColor};
    }
`

const CMDButtonDisabled = styled.div`
    ${tw`p-1 transition-all rounded-br`}
`

// fuck tailwind
const textSizeMap = {
    'text-xs': tw`text-xs`,
    'text-sm': tw`text-sm`,
    'text-base': tw`text-base`,
    'text-lg': tw`text-lg`,
    'text-xl': tw`text-xl`,
}

const ComponentButton = styled(Button)`
    ${tw`transition-all border-none`};
    background-color: ${mcPasteStyle.textButtonColor};

    &:hover {
        background-color: ${mcPasteStyle.textButtonHoverColor};
    }
`

const copyData = (content: string) => {
    function copyNavigator () {
        navigator.clipboard.writeText(content);
    }

    function copyHtml () {
        const area = document.createElement("textarea");
        area.value = content;
        area.style.position = "fixed";
        document.body.appendChild(area);
        area.focus();
        area.select();
        area.setSelectionRange(0, 99999);
        document.execCommand("copy");
        document.body.removeChild(area);
    }

    if (navigator.clipboard !== undefined) copyNavigator()
    else copyHtml()
}

export default ({ position }: Props) => {
    const [ log, setLog ] = useState<string[]>([]);
    const addLog = (data: string) => setLog(prevLog => [...prevLog, data.startsWith(">") ? data.substring(1) : data])

    const [ uploading, setUploading ] = useState(false)
    const [ copied, setCopied ] = useState<false | PasteResponse>(false)
    const { connected, instance } = ServerContext.useStoreState(state => state.socket);

    const uuid = ServerContext.useStoreState(state => state.server.data!.uuid)

    useEffect(() => {
        if (!connected || !instance) return

        instance.addListener(SocketEvent.CONSOLE_OUTPUT, addLog);

        return () => {
            instance.removeListener(SocketEvent.CONSOLE_OUTPUT, addLog);
        }
    }, [ connected, instance ])

    const [ toastText, setToastText ] = useState("")

    useEffect(() => {
        if (!copied) return
        if (copied.key) {
            setToastText(mcPasteStyle.toastText.replace("%key%", copied.key));
        } else if (copied.error) {
            setToastText(mcPasteStyle.toastErrorText.replace("%error%", copied.error))
        }
    }, [ copied ])

    const resetStateAfter = (ms = 2500) => {
        setTimeout(() => {
            setCopied(false)
            setUploading(false);
        }, ms);
    }

    const mcPaste = () => {
        if (uploading) return
        const data = stripAnsi(log.map(it => it.replace("\r", "")).join("\n")) || "";
        setUploading(true);
        shareServerLog(uuid, data)
            .then((response: PasteResponse): PasteResponse => {
                if (response.key) {
                    copyData(`https://${response.domain ?? "mcpaste.com"}/${response.key}`)
                }
                return response;
            }).then((response) => {
            setCopied(response);
            resetStateAfter();
        })
            .catch((err) => {
                console.log(err);
                setCopied({ error: "Unexpected error...." })
                resetStateAfter()
            })
    }

    const CMDButtonType = uploading ? CMDButtonDisabled : CMDButton

    const content = position === 'component' ?
        (
            <div css={[tw`rounded p-4 flex justify-center`, mcPasteStyle.shadow ? tw`shadow-lg` : '']} style={{ backgroundColor: mcPasteStyle.boxColor }}>
                <ComponentButton
                    size={mcPasteStyle.buttonSize}
                    isSecondary
                    css={tw`mr-2 shadow-md`}
                    disabled={uploading}
                    onClick={e => {
                        mcPaste()
                    }}
                >
                    <div css={textSizeMap[mcPasteStyle.textSize]} style={{ color: mcPasteStyle.textColor }}>
                        { mcPasteStyle.textButtonText }
                    </div>
                </ComponentButton>
            </div>
        )
        :
        (
            <div
                 css={[uploading ? tw`opacity-75` : tw`cursor-pointer`]}
                 onClick={e => {
                     mcPaste()
                 }}
            >
                <FontAwesomeIcon icon={mcPasteStyle.icon as IconProp} fixedWidth color={mcPasteStyle.iconColor} />
            </div>
        )

    return (
        <div css={position === "component" ? tw`col-span-6` : ""}>
            { content }
            <SwitchTransition>
                <Fade timeout={250} key={copied !== false ? 'visible' : 'invisible'}>
                    {copied !== false ?
                        <Toast>
                            <div>
                                <p css={tw`text-lg`}>{toastText}</p>
                            </div>
                        </Toast>
                        : <></>
                    }
                </Fade>
            </SwitchTransition>
        </div>
    )
}
